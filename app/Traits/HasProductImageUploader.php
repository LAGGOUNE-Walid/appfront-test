<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait HasProductImageUploader
{
    public function upload(?UploadedFile $image = null): string
    {
        if (! $image) {
            return 'product-placeholder.jpg';
        }
        $folder = 'uploads';
        $folderPath = public_path($folder);
        $finalFileName = time().'.'.$image->getClientOriginalExtension();
        $image->move($folderPath, $finalFileName);

        return $folder.'/'.$finalFileName;
    }
}
