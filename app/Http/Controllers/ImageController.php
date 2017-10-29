<?php

namespace App\Http\Controllers;

use File;
use Response;
use Image;

class ImageController extends Controller
{
    public function view($filename)
    {
        $image = $this->getImage($filename);

        return $image->response();
    }

    public function resize($width, $height, $filename)
    {
        $image = $this->getImage($filename);

        $image->fit($width, $height);

        return $image->response();
    }

    public function crop($left, $top, $width, $height, $filename)
    {
        $image = $this->getImage($filename);

        $image->crop($width, $height, $left, $top);

        return $image->response();
    }

    private function getImage($filename)
    {
        $path = storage_path().'/app/'.$filename;

        if (!File::exists($path)) {
            abort(404);
        }
        $image = Image::make($path);

        return $image;
    }
}
