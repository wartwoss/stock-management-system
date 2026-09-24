<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Dump the database and upload to Google Drive via rclone';

    public function handle(): int
    {
        $dbName     = config('database.connections.mysql.database');
        $dbUser     = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');
        $dbHost     = config('database.connections.mysql.host');
        $dbPort     = config('database.connections.mysql.port', 3306);

        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            $this->error('mysqldump not found. Make sure XAMPP is installed.');
            return self::FAILURE;
        }

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = $dbName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $passwordFlag = $dbPassword ? "-p{$dbPassword}" : '';
        $cmd = "\"{$mysqldump}\" -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passwordFlag} {$dbName}";

        $this->info("Creating database dump: {$filename}");
        exec("{$cmd} > \"{$filepath}\" 2>&1", $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($filepath) || filesize($filepath) < 100) {
            $this->error('mysqldump failed: ' . implode("\n", $output));
            if (file_exists($filepath)) unlink($filepath);
            return self::FAILURE;
        }

        $size = round(filesize($filepath) / 1024, 1);
        $this->info("Dump saved ({$size} KB). Uploading to Google Drive...");

        $rclone = $this->findRclone();
        if (!$rclone) {
            $this->warn('rclone not found. Backup saved locally only.');
            $this->warn('Run launcher/first-time-setup.bat to install rclone.');
            return self::SUCCESS;
        }

        $driveFolder = env('BACKUP_GDRIVE_FOLDER', 'stockflow-backups');
        $rcloneCmd   = "\"{$rclone}\" copy \"{$filepath}\" \"gdrive:{$driveFolder}\" 2>&1";

        exec($rcloneCmd, $rcloneOutput, $rcloneExit);

        if ($rcloneExit !== 0) {
            $this->error('rclone upload failed: ' . implode("\n", $rcloneOutput));
            return self::FAILURE;
        }

        $this->info("Backup uploaded to Google Drive: {$driveFolder}/{$filename}");
        $this->pruneOldBackups($backupDir, 30);
        return self::SUCCESS;
    }

    private function findMysqldump(): ?string
    {
        $candidates = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'mysqldump',
        ];
        foreach ($candidates as $p) {
            if (file_exists($p)) return $p;
        }
        return null;
    }

    private function findRclone(): ?string
    {
        $candidates = [
            base_path('launcher\\rclone\\rclone.exe'),
            'C:\\rclone\\rclone.exe',
            'rclone',
        ];
        foreach ($candidates as $p) {
            if (file_exists($p)) return $p;
        }
        return null;
    }

    private function pruneOldBackups(string $dir, int $keep): void
    {
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        if (!$files || count($files) <= $keep) return;
        usort($files, fn($a, $b) => filemtime($a) - filemtime($b));
        foreach (array_slice($files, 0, count($files) - $keep) as $f) unlink($f);
    }
}
