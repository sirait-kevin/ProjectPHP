<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\UserData;

class CSVWriter
{

    public function __construct(private string $filePath) {}

    public function write(UserData $userData): void
    {
        $fileExists = file_exists($this->filePath);

        $file = fopen($this->filePath, 'a');
        if ($file === false) {
            throw new \Exception("Failed to open CSV file for writing: {$this->filePath}");
        }


        if (!$fileExists || !filesize($this->filePath)) {
            fputcsv($file, array_keys((array) $userData), ",", "\"", "\\", "\n");
        }

        fputcsv($file, (array) $userData, ",", "\"", "\\", "\n");
        fclose($file);
    }
}
