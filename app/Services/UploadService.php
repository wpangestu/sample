<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class UploadService
{
    public function compressImage($uploadFoder, $requestImage,$quality=50){
        $uploadFolder = $uploadFoder;
        $name_file = time().'.'.$requestImage->getClientOriginalExtension();

        $folder = public_path('storage/'.$uploadFolder);
        if (!Storage::exists('public/'.$uploadFolder)) {
            Storage::makeDirectory('public/'.$uploadFolder, 0775, true, true);
        }
        Image::make($requestImage)->save($folder.'/'.$name_file,$quality,'jpeg');
        $media = Storage::disk('public')->url($uploadFolder."/".$name_file);
        return $media;
    }
}