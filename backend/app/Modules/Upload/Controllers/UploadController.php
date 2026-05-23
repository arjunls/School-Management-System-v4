<?php

namespace App\Modules\Upload\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Upload\Requests\UploadPhotoRequest;
use App\Modules\Upload\Requests\UploadDocumentRequest;
use Illuminate\Support\Facades\Storage;

/**
 * @group Uploads
 *
 * APIs for file uploads
 */
class UploadController extends Controller
{
    /**
     * Upload a photo file
     */
    public function uploadPhoto(UploadPhotoRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('photos', 'public');

        $url = Storage::url($path);

        return $this->success([
            'path' => $path,
            'url' => $url,
        ], 'File uploaded successfully');
    }

    /**
     * Upload a document file
     */
    public function uploadDocument(UploadDocumentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $url = Storage::url($path);

        return $this->success([
            'path' => $path,
            'url' => $url,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ], 'Document uploaded successfully');
    }
}
