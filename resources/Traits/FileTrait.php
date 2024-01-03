<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    /**
     * Uploads a file to the specified directory and returns the file path.
     *
     * @param mixed $file The file to upload.
     * @param string $directory The directory where the file should be stored.
     *
     * @return string The file path of the uploaded file.
     */
    protected function uploadFile(mixed $file, string $directory): string
    {
        $fileName = $this->getFileName($file);

        $realPath = $directory . $fileName;

        Storage::disk('public')->put($realPath, file_get_contents($file));

        $filePath   = 'storage' . $realPath;


        $request = request();
        // Add the uploaded file path to the request to enable file cleanup in case of exceptions
        $request->merge(['uploadedFiles' => array_merge($request->input('uploadedFiles', []), [$filePath])]);

        return $filePath;
    }

    /**
     * Generates a unique file name based on the current timestamp and the original file name.
     *
     * @param object $file The file object.
     *
     * @return string The generated file name.
     */
    protected function getFileName(object $file): string
    {
        return  Carbon::now()->format('Y_m_d_u') . '_' . $file->getClientOriginalName();
    }

    /**
     * Deletes a file from the public directory.
     *
     * @param string $fileName The file name or path to delete.
     *
     * @return bool Returns true if the file was successfully deleted, or false if the file does not exist or couldn't be deleted.
     */
    protected function deleteFile($fileName): bool
    {
        if (file_exists(public_path($fileName))) {
            unlink(public_path($fileName));
            return true;
        }
        return false;
    }

    /**
     * Retrieves the file extension from the given file path.
     *
     * @param string $filePath The file path from which to extract the extension.
     *
     * @return string The file extension, or an empty string if the file extension couldn't be determined.
     */
    protected function getFileExtension(string $filePath): string
    {
        $infoPath = pathinfo(public_path($filePath));

        return $infoPath['extension'];
    }
}
