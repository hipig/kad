<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadRequest;
use App\Http\Resources\UploadResource;
use App\Models\Upload;
use Illuminate\Http\Request;

class UploadsController extends Controller
{
    public function store(UploadRequest $request)
    {
        $file = $request->file('file');
        $upload = new Upload([
            'disk' => $request->disk ?? Upload::DEFAULT_DISK,
            'type' => $request->type ?? Upload::UPLOAD_TYPE_IMAGE,
            'name' => $file->getClientOriginalName() ,
            'extension' => $file->getClientOriginalExtension(),
        ]);
        $upload->upload($file);

        return UploadResource::make($upload);
    }
}
