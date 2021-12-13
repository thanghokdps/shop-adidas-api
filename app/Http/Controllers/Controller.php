<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function viewImage($id, $name): \Illuminate\Http\Response
    {
        $path = "product/$id/$name";
        $pathImage = Storage::disk('public')->path($path);
        $image = File::get($pathImage);
        $type = File::mimeType($pathImage);
        $response = Response::make($image, 200);
        $response->header("Content-Type", $type);
        return $response;
    }

    public function viewImageComment($id, $name): \Illuminate\Http\Response
    {
        $path = "comment/$id/$name";
        $pathImage = Storage::disk('public')->path($path);
        $image = File::get($pathImage);
        $type = File::mimeType($pathImage);
        $response = Response::make($image, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
