<?php

namespace App\Services;

use App\Models\UserGoogleToken;
use Google\Client;
use Google\Service\Drive;
use GuzzleHttp\Client as GuzzleClient;

class GoogleDriveService
{
    protected Client $client;
    protected ?Drive $driveService = null;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setAccessType('offline');
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->setHttpClient(new GuzzleClient([
            'timeout' => (int) config('services.google.request_timeout', 900),
            'connect_timeout' => (int) config('services.google.connect_timeout', 30),
        ]));
    }

    public function setAccessToken(UserGoogleToken $token): self
    {
        $this->client->setAccessToken([
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in' => $token->expires_at->diffInSeconds(now(), false) > 0 ? $token->expires_at->diffInSeconds(now()) : 0,
            'created' => $token->updated_at->timestamp,
        ]);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken();
                $token->update([
                    'access_token' => $newToken['access_token'],
                    'expires_at' => now()->addSeconds($newToken['expires_in']),
                ]);
            }
        }

        $this->driveService = new Drive($this->client);

        return $this;
    }

    public function findFolderByName(string $name, ?string $parentId = null): ?string
    {
        $q = "mimeType='application/vnd.google-apps.folder' and name='" . str_replace("'", "\\'", $name) . "' and trashed=false";

        if ($parentId) {
            $q .= " and '" . $parentId . "' in parents";
        }

        $response = $this->driveService->files->listFiles([
            'q' => $q,
            'spaces' => 'drive',
            'fields' => 'files(id, name)',
        ]);

        if (count($response->files) == 0) {
            return null;
        }

        return $response->files[0]->id;
    }

    public function createFolder(string $name, ?string $parentId = null): string
    {
        $metadata = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ];

        if ($parentId) {
            $metadata['parents'] = [$parentId];
        }

        $fileMetadata = new Drive\DriveFile($metadata);
        $folder = $this->driveService->files->create($fileMetadata, ['fields' => 'id']);

        return $folder->id;
    }

    public function uploadFile($file, string $folderId, ?string $customName = null): array
    {
        return $this->uploadFromPath(
            $file->getRealPath(),
            $folderId,
            $customName ?? $file->getClientOriginalName(),
            $file->getMimeType() ?: 'application/octet-stream',
            (int) $file->getSize()
        );
    }

    public function uploadLocalFile(string $absolutePath, string $folderId, string $customName, ?string $mimeType = null): array
    {
        if (!is_file($absolutePath)) {
            throw new \RuntimeException('Upload source file not found: ' . $absolutePath);
        }

        return $this->uploadFromPath(
            $absolutePath,
            $folderId,
            $customName,
            $mimeType ?: (mime_content_type($absolutePath) ?: 'application/octet-stream'),
            (int) filesize($absolutePath)
        );
    }

    protected function uploadFromPath(
        string $filePath,
        string $folderId,
        string $fileName,
        string $mimeType,
        int $fileSize
    ): array {
        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName,
            'parents' => [$folderId],
        ]);

        // Defer MUST be enabled before calling files->create()
        // so we receive RequestInterface for resumable upload.
        $this->driveService->getClient()->setDefer(true);

        $request = $this->driveService->files->create($fileMetadata, [
            'fields' => 'id, name, size, mimeType, webViewLink',
        ]);

        $chunkSize = (int) config('services.google.upload_chunk_size', 8 * 1024 * 1024);
        $chunkSize = max(256 * 1024, $chunkSize);

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Failed to open file stream for upload.');
        }

        $uploadedFile = false;

        try {
            $media = new \Google\Http\MediaFileUpload(
                $this->driveService->getClient(),
                $request,
                $mimeType,
                null,
                true,
                $chunkSize
            );
            $media->setFileSize($fileSize);

            while (!$uploadedFile && !feof($handle)) {
                $chunk = fread($handle, $chunkSize);
                if ($chunk === false) {
                    throw new \RuntimeException('Failed to read file chunk.');
                }
                $uploadedFile = $media->nextChunk($chunk);
            }
        } finally {
            fclose($handle);
            $this->driveService->getClient()->setDefer(false);
        }

        if ($uploadedFile === false) {
            throw new \RuntimeException('Google Drive upload did not complete.');
        }

        return [
            'id' => $uploadedFile->id,
            'name' => $uploadedFile->name,
            'size' => $fileSize,
            'mimeType' => $uploadedFile->mimeType,
            'url' => $uploadedFile->webViewLink,
        ];
    }

    public function deleteFile(string $fileId): void
    {
        $this->driveService->files->delete($fileId);
    }
}
