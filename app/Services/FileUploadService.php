<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Store an uploaded file and return the generated filename.
     */
    public function store(?object $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        $extension = $file->extension();
        if (empty($extension)) {
            $extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        }

        // Hardened extension blacklist to prevent RCE
        $blacklist = [
            'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar', 'phtml',
            'exe', 'bat', 'cmd', 'sh', 'pl', 'py', 'cgi', 'htaccess', 'js'
        ];

        if (in_array(strtolower($extension), $blacklist, true)) {
            throw new \InvalidArgumentException('Uploaded file extension is not allowed.');
        }

        // Cryptographically secure, unpredictable filename to prevent IDOR / enumeration
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $file->storeAs(rtrim($directory, '/') . '/', $filename, 'public');

        return $filename;
    }

    /**
     * Replace an existing file: delete old, store new, return new filename (or keep old).
     */
    public function replace(?object $file, string $directory, ?string $existingFilename): ?string
    {
        if (! $file) {
            return $existingFilename;
        }

        $this->delete($directory, $existingFilename);

        return $this->store($file, $directory);
    }

    /**
     * Delete a file from the public disk if it exists.
     */
    public function delete(string $directory, ?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = rtrim($directory, '/') . '/' . $filename;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
