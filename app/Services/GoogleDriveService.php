<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use App\Models\UserGoogleToken;

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
        $q = "mimeType='application/vnd.google-apps.folder' and name='" . str_replace("'", "\'", $name) . "' and trashed=false";
        
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

        // Google Drive API v3 tidak menerima 'parents' kosong ([]).
        // Hanya sertakan jika parentId ada.
        if ($parentId) {
            $metadata['parents'] = [$parentId];
        }

        $fileMetadata = new Drive\DriveFile($metadata);

        $folder = $this->driveService->files->create($fileMetadata, ['fields' => 'id']);
        return $folder->id;
    }

    public function uploadFile($file, string $folderId, ?string $customName = null): array
    {
        $fileMetadata = new Drive\DriveFile([
            'name'    => $customName ?? $file->getClientOriginalName(),
            'parents' => [$folderId],
        ]);

        $filePath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        // Gunakan resumable/chunked upload (streaming) — file tidak dimuat ke RAM sekaligus.
        // Ini memungkinkan upload file besar tanpa membebani memory hosting.
        $this->driveService->getClient()->setDefer(true);

        $request = $this->driveService->files->create($fileMetadata, [
            'fields' => 'id, name, size, mimeType, webViewLink',
        ]);

        $chunkSize = 5 * 1024 * 1024; // 5 MB per chunk
        $media = new \Google\Http\MediaFileUpload(
            $this->driveService->getClient(),
            $request,
            $mimeType,
            null,
            true,        // resumable = true
            $chunkSize
        );
        $media->setFileSize($fileSize);

        // Stream file per chunk — RAM hanya terpakai 5MB setiap saat
        $uploadedFile = false;
        $handle = fopen($filePath, 'rb');

        while (!$uploadedFile && !feof($handle)) {
            $chunk       = fread($handle, $chunkSize);
            $uploadedFile = $media->nextChunk($chunk);
        }

        fclose($handle);

        // Hapus file temp dari disk setelah berhasil diupload ke Google Drive
        // agar file tidak menumpuk di folder temporary PHP/sistem
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Kembalikan ke mode normal (non-deferred)
        $this->driveService->getClient()->setDefer(false);

        return [
            'id'       => $uploadedFile->id,
            'name'     => $uploadedFile->name,
            'size'     => $fileSize,
            'mimeType' => $uploadedFile->mimeType,
            'url'      => $uploadedFile->webViewLink,
        ];
    }

    public function deleteFile(string $fileId): void
    {
        $this->driveService->files->delete($fileId);
    }
}
