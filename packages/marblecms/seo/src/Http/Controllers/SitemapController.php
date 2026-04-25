<?php

namespace MarbleCms\Seo\Http\Controllers;

use Illuminate\Routing\Controller;
use MarbleCms\Seo\Services\SitemapService;

class SitemapController extends Controller
{
    public function __invoke(SitemapService $sitemapService)
    {
        $xml = $sitemapService->generate();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
