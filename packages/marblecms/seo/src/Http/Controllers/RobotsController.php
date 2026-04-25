<?php

namespace MarbleCms\Seo\Http\Controllers;

use Illuminate\Routing\Controller;

class RobotsController extends Controller
{
    public function __invoke()
    {
        $content = config('seo.robots_txt');

        if ($content === null) {
            return response(view('seo::robots')->render(), 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        if (is_array($content)) {
            $content = implode("\n", $content);
        }

        return response((string) $content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
