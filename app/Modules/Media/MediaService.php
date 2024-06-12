<?php

namespace App\Modules\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class MediaService
{
    /**
     * Handle the upload of a media file and associate it with a model.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \App\Models\Media
     */
    public function upload(UploadedFile $file, Model $model)
    {
        // $path = $file->store('event_image', 'public');
        
        // // Generate a hash of the file
        // $hash = md5_file($file->getRealPath());

        // // Create a new Media instance
        // $media = new Media([
        //     'hash' => $hash,
        //     'path' => $path,
        //     'format' => $file->getClientOriginalExtension(),
        //     'mime_type' => $file->getClientMimeType(),
        //     'size' => $file->getSize(),
        // ]);

        // Store the file and get the storage path
        $path = Storage::disk('public')->put('event_image', $file);
        $hash = md5_file($file->getRealPath());

        $media = new Media([
            'hash' => $hash,
            'path' => $path,
            'format' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        // Associate the media with the given model
        $model->media()->save($media);

        return $media;
    }
}