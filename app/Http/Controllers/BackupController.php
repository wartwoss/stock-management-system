<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function run(): JsonResponse
    {
        $exitCode = Artisan::call('db:backup');
        $output   = Artisan::output();

        if ($exitCode !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed.',
                'detail'  => $output,
            ], 500);
        }

        // Find latest backup file
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.sql');
        $latestFile = $files ? basename(end($files)) : null;

        return response()->json([
            'success'      => true,
            'message'      => 'Backup completed and uploaded to Google Drive.',
            'filename'     => $latestFile,
            'backed_up_at' => now()->toISOString(),
            'detail'       => $output,
        ]);
    }

    public function status(): JsonResponse
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.sql');

        if (!$files) {
            return response()->json([
                'last_backup'  => null,
                'backup_count' => 0,
            ]);
        }

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        $latest = $files[0];

        return response()->json([
            'last_backup'  => date('Y-m-d H:i:s', filemtime($latest)),
            'filename'     => basename($latest),
            'backup_count' => count($files),
            'size_kb'      => round(filesize($latest) / 1024, 1),
        ]);
    }
}
