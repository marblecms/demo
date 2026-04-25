<?php

namespace MarbleCms\Newsletter\Http\Controllers\Public;

use Illuminate\Routing\Controller;
use MarbleCms\Newsletter\Models\CampaignClick;
use MarbleCms\Newsletter\Models\CampaignOpen;
use MarbleCms\Newsletter\Models\CampaignSend;

class TrackingController extends Controller
{
    /**
     * Record an open event and return a 1x1 transparent GIF pixel.
     */
    public function open(string $token)
    {
        $send = CampaignSend::where('token', $token)->first();

        if ($send) {
            CampaignOpen::create([
                'send_id'    => $send->id,
                'opened_at'  => now(),
                'ip'         => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 500),
            ]);
        }

        // 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    /**
     * Record a click event and redirect to the original URL.
     * The URL is base64-encoded in the route parameter.
     */
    public function click(string $token, string $encodedUrl)
    {
        $send = CampaignSend::where('token', $token)->first();
        $url  = base64_decode($encodedUrl);

        if ($send && $url) {
            CampaignClick::create([
                'send_id'    => $send->id,
                'url'        => $url,
                'clicked_at' => now(),
                'ip'         => request()->ip(),
            ]);
        }

        return redirect($url ?: '/');
    }
}
