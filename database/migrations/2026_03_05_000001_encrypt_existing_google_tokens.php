<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Re-enkripsi token Google yang sudah ada (sebelumnya tersimpan sebagai plain text).
     * Diperlukan setelah menambahkan cast 'encrypted' pada model UserGoogleToken.
     */
    public function up(): void
    {
        $tokens = DB::table('user_google_tokens')->get();

        foreach ($tokens as $token) {
            try {
                // Coba decrypt dulu — jika berhasil, sudah terenkripsi (skip)
                Crypt::decryptString($token->access_token);
                // Jika tidak throw exception, berarti sudah terenkripsi
            } catch (\Exception $e) {
                // Plain text — perlu dienkripsi
                $updateData = [
                    'access_token' => Crypt::encryptString($token->access_token),
                ];

                // Enkripsi refresh_token jika ada
                if ($token->refresh_token) {
                    try {
                        Crypt::decryptString($token->refresh_token);
                        // Sudah terenkripsi, skip
                    } catch (\Exception $e2) {
                        $updateData['refresh_token'] = Crypt::encryptString($token->refresh_token);
                    }
                }

                DB::table('user_google_tokens')
                    ->where('id', $token->id)
                    ->update($updateData);
            }
        }
    }

    /**
     * Reverse the migration.
     * Mendekripsi kembali ke plain text (untuk rollback).
     */
    public function down(): void
    {
        $tokens = DB::table('user_google_tokens')->get();

        foreach ($tokens as $token) {
            try {
                $decryptedAccess = Crypt::decryptString($token->access_token);
                $updateData = ['access_token' => $decryptedAccess];

                if ($token->refresh_token) {
                    try {
                        $updateData['refresh_token'] = Crypt::decryptString($token->refresh_token);
                    } catch (\Exception $e) {
                        // skip jika gagal
                    }
                }

                DB::table('user_google_tokens')
                    ->where('id', $token->id)
                    ->update($updateData);
            } catch (\Exception $e) {
                // Sudah plain text, skip
            }
        }
    }
};
