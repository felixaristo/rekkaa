<?php
namespace App\Libraries;

use Error;
use Illuminate\Support\Facades\Storage;

class AppUploadLibrary {

    public function uploadFile($file, $foldername = 'images/default', $disk = 's3')
    {
        // Generate a unique filename for the photo
        $filename = uniqid() . '.' . $file->extension();

        try {
            $foldername = env('APP_ENV').'/'.$foldername;
            // Upload the photo to Amazon S3
            $filePath = $file->storeAs($foldername, $filename, ['disk' => $disk]);

            // Return the S3 URL of the saved image
            return [
                'success' => true,
                'data' => [
                    'filename' => $filename,
                    'url' => Storage::disk('s3')->url($filePath),
                    'base64' => base64_encode(file_get_contents($file)),
                ]
            ];
        } catch(Error $e) {
            return [
                'success' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteFile($filename = 'images/default/xyz.png', $isfullpath = false)
    {
        $filename = env('APP_ENV').'/'.$filename;
        
        try {
            // Delete the photo to Amazon S3
            if($isfullpath) {
                $filename = str_replace('https://rekkaa.s3.ap-southeast-3.amazonaws.com/', '', $filename);
            }
            Storage::disk('s3')->delete($filename);

            // Return the S3 URL of the saved image
            return [
                'success' => true,
                'data' => [
                    'url' => $filename
                ]
            ];
        } catch(Error $e) {
            return [
                'success' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}