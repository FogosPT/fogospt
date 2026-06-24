<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Subclasses set this to look up SEO copy from pages.seo.{seoKey}
    // (e.g. 'home', 'madeira', 'list'). FireController bypasses this and
    // builds the title from the fire payload instead.
    protected $seoKey = 'home';

    protected function generateMetadata()
    {
        $brand = __('pages.seo.brand_suffix');

        if (get_class($this) === \App\Http\Controllers\FireController::class) {
            $location = trim((string) @$this->fire['location']);
            $concelho = trim((string) @$this->fire['concelho']);
            $status   = trim((string) @$this->fire['status']);
            // Snapshot timestamp baked in at render time: fire pages are
            // CDN-cached and the meio counts drift, so the meta needs to
            // say "as of X" — otherwise stale snippets in SERPs/social
            // misrepresent the situation.
            $date = date('d-m-Y');
            $hour = date('H:i');

            $titleKey = $concelho !== '' ? 'pages.seo.fire.title' : 'pages.seo.fire.title_no_concelho';
            $title = __($titleKey, [
                'date'     => $date,
                'hour'     => $hour,
                'location' => $location !== '' ? $location : '—',
                'concelho' => $concelho,
                'status'   => $status !== '' ? $status : '—',
            ]);
            $description = __('pages.seo.fire.description', [
                'date'     => $date,
                'hour'     => $hour,
                'location' => $location !== '' ? $location : '—',
                'concelho' => $concelho !== '' ? $concelho : ($location !== '' ? $location : '—'),
                'status'   => $status !== '' ? $status : '—',
                'man'      => (int) @$this->fire['man'],
                'terrain'  => (int) @$this->fire['terrain'],
                'aerial'   => (int) @$this->fire['aerial'],
            ]);
        } else {
            $title       = __('pages.seo.' . $this->seoKey . '.title');
            $description = __('pages.seo.' . $this->seoKey . '.description');
        }

        return [
            'pageTitle'   => $title . $brand,
            'title'       => $title,
            'description' => $description,
            'url'         => "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
        ];
    }
}
