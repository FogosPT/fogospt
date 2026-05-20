<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenericController extends Controller
{
    protected $pageName;

    /**
     * @return mixed
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param mixed $pageName
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }

    public function getIndex()
    {
        $this->setPageName('Início');
        return view('index')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getIndexMadeira()
    {
        $this->setPageName('Madeira - Início');
        return view('index-madeira')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getOtherFires()
    {
        $this->setPageName('Outros incêndios');
        return view('other-fires')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getAbout()
    {
        $this->setPageName(__('includes.menu.about'));
        return view('about')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getInformation()
    {
        $this->setPageName(__('includes.menu.information'));
        return view('information')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getNotifications()
    {
        $this->setPageName(__('includes.menu.notifications'));
        return view('notifications')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getManifest()
    {
        $this->setPageName(__('includes.menu.manifest'));
        return view('manifest')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getPartnerships()
    {
        $this->setPageName(__('includes.menu.partnerships'));
        return view('partnerships')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getList()
    {
        $this->setPageName(__('includes.menu.list'));
        $fires = LegacyApi::getFires();

        return view('list', ['data' => @$fires['data']])->with(['metadata' => $this->generateMetadata()]);
    }

    public function getTable()
    {
        $this->setPageName(__('includes.menu.table'));
        $fires = LegacyApi::getFires();

        return view('table', ['data' => @$fires['data']])->with(['metadata' => $this->generateMetadata()]);
    }

    public function getWarnings()
    {
        $this->setPageName(__('includes.menu.warnings'));
        $warnings = LegacyApi::getWarnings();

        return view('warnings', ['data' => $warnings['data']])->with(['metadata' => $this->generateMetadata()]);
    }

    public function getWarningsMadeira()
    {
        $this->setPageName(__('includes.menu.warnings'));
        $warnings = LegacyApi::getWarningsMadeira();

        return view('warnings-madeira', ['data' => $warnings['data']])->with(['metadata' => $this->generateMetadata()]);
    }

    public function getStats()
    {
        $this->setPageName(__('includes.menu.stats'));
        $now = LegacyApi::getNow();
        $data = array(
            'now' => $now['data'],
        );

        return view('stats', ['data' => $data])->with(['metadata' => $this->generateMetadata()]);
    }

    public function api()
    {
        $this->setPageName(__('api-docs.meta_title'));

        return view('api')->with(['metadata' => $this->generateMetadata()]);
    }

    public function apiTerms()
    {
        $this->setPageName(__('api-terms.meta_title'));

        return view('api-terms')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getPrivacyPolicy()
    {
        $this->setPageName('Política de Privacidade');

        return view('privacy-policy')->with(['metadata' => $this->generateMetadata()]);
    }

    public function subscribe(Request $request)
    {
        $token = $request->get('token');
        $topic = $this->resolveTopic($request->get('topic'));

        if (!$token || !$topic) {
            return \Response::json(['success' => false], 400);
        }

        [$httpcode, $body] = $this->fcmIidRequest(
            "https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}",
            null
        );

        if ($httpcode !== 200) {
            Log::warning('FCM topic subscribe failed', [
                'topic'    => $topic,
                'httpcode' => $httpcode,
                'response' => $body,
            ]);
        }

        return \Response::json(['success' => $httpcode === 200]);
    }

    public function unsubscribe(Request $request)
    {
        $token = $request->get('token');
        $topic = $this->resolveTopic($request->get('topic'));

        if (!$token || !$topic) {
            return \Response::json(['success' => false], 400);
        }

        [$httpcode, $body] = $this->fcmIidRequest(
            'https://iid.googleapis.com/iid/v1:batchRemove',
            json_encode([
                'to' => "/topics/{$topic}",
                'registration_tokens' => [$token],
            ])
        );

        if ($httpcode !== 200) {
            Log::warning('FCM topic unsubscribe failed', [
                'topic'    => $topic,
                'httpcode' => $httpcode,
                'response' => $body,
            ]);
        }

        return \Response::json(['success' => $httpcode === 200]);
    }

    private function fcmIidRequest($url, $jsonBody)
    {
        $accessToken = $this->fcmAccessToken();
        if (!$accessToken) {
            return [0, 'FCM OAuth token unavailable'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'access_token_auth: true',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody === null ? '' : $jsonBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $body = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$httpcode, $body];
    }

    /**
     * Obtain (and cache) a Google OAuth2 access token for the FCM scope,
     * signed with the project's service account JSON. Required because the
     * legacy `Authorization: key=<server-key>` flow was deprecated by Google.
     *
     * Service account JSON path is read from FIREBASE_SERVICE_ACCOUNT_PATH
     * (absolute path) or GOOGLE_APPLICATION_CREDENTIALS as fallback.
     */
    private function fcmAccessToken()
    {
        return Cache::remember('fcm_oauth_access_token', 3300, function () {
            $path = env('FIREBASE_SERVICE_ACCOUNT_PATH') ?: env('GOOGLE_APPLICATION_CREDENTIALS');
            if (!$path || !is_readable($path)) {
                Log::error('FCM OAuth: service account JSON not found', ['path' => $path]);
                return null;
            }

            $sa = json_decode(file_get_contents($path), true);
            if (!isset($sa['client_email'], $sa['private_key'], $sa['token_uri'])) {
                Log::error('FCM OAuth: malformed service account JSON');
                return null;
            }

            $now = time();
            $header  = ['alg' => 'RS256', 'typ' => 'JWT'];
            $payload = [
                'iss'   => $sa['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => $sa['token_uri'],
                'iat'   => $now,
                'exp'   => $now + 3600,
            ];

            $b64 = function ($data) {
                return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
            };
            $signingInput = $b64(json_encode($header)) . '.' . $b64(json_encode($payload));
            $signature = '';
            if (!openssl_sign($signingInput, $signature, $sa['private_key'], 'sha256WithRSAEncryption')) {
                Log::error('FCM OAuth: JWT signing failed', ['err' => openssl_error_string()]);
                return null;
            }
            $jwt = $signingInput . '.' . $b64($signature);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sa['token_uri']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($resp, true);
            if ($code !== 200 || !isset($data['access_token'])) {
                Log::error('FCM OAuth: token exchange failed', ['code' => $code, 'body' => $resp]);
                return null;
            }
            return $data['access_token'];
        });
    }

    /**
     * Serve the Firebase Cloud Messaging service worker with the app config
     * injected from env. Kept behind a route (instead of a static file in
     * public/) so the same env-driven appId / VAPID values used by the page
     * stay in sync with the SW. The Service-Worker-Allowed header makes sure
     * the SW can control the whole origin.
     */
    public function firebaseMessagingSw()
    {
        $appId = env('FIREBASE_APP_ID', '');

        $config = json_encode([
            'apiKey'            => 'AIzaSyCxxu_jTrBrGE8Em1kaqn3wTbCBa8_Ra7M',
            'authDomain'        => 'admob-app-id-6663345165.firebaseapp.com',
            'databaseURL'       => 'https://admob-app-id-6663345165.firebaseio.com',
            'projectId'         => 'admob-app-id-6663345165',
            'storageBucket'     => 'admob-app-id-6663345165.appspot.com',
            'messagingSenderId' => '726949968874',
            'appId'             => $appId,
        ], JSON_UNESCAPED_SLASHES);

        $js = <<<JS
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

firebase.initializeApp({$config});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    const n = (payload && payload.notification) || {};
    const data = (payload && payload.data) || {};

    if (!n.title && !n.body) {
        return Promise.resolve();
    }

    const title = n.title || 'Fogos.pt';
    const options = {
        body: n.body || '',
        icon: n.icon || '/img/logo.svg',
        data: data,
    };

    if (data.fireId) {
        options.data.click_url = '/fogo/' + data.fireId;
    }

    return self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.click_url) || '/';
    event.waitUntil(clients.openWindow(url));
});
JS;

        return response($js, 200, [
            'Content-Type'           => 'application/javascript',
            'Service-Worker-Allowed' => '/',
            'Cache-Control'          => 'no-cache, max-age=0, must-revalidate',
        ]);
    }

    /**
     * Resolve a client-provided topic value to the canonical FCM topic name.
     *
     * Whitelists topics from the unified catalogue and prefixes with `dev-`
     * outside of production, matching the backend's environment scheme.
     */
    private function resolveTopic($topic)
    {
        if (!is_string($topic) || $topic === '') {
            return null;
        }

        // DICO topics accept both 4-digit distrito codes (`<dd>00`, e.g. 1100)
        // and 6-digit concelho codes (`<dd><cc>00`, e.g. 110600), always ending
        // in "00" — matching the convention used by the fogos backend / mobile.
        $allowed =
            $topic === 'incident-important'
            || $topic === 'warnings'
            || $topic === 'planes'
            || $topic === 'madeira'
            || preg_match('/^incident-[A-Za-z0-9_-]{1,64}$/', $topic)
            || preg_match('/^district-(\d{2}|\d{4})00$/', $topic)
            || preg_match('/^district-all-(\d{2}|\d{4})00$/', $topic);

        if (!$allowed) {
            return null;
        }

        $prefix = env('APP_ENV') === 'production' ? '' : 'dev-';
        return $prefix . $topic;
    }

    /**
     * Sets the app language based on user action
     * in the case user try to set the app language
     * to a language that we dont have a record as supported language
     * it will fallback for the default app locale
     *
     * @param Request $request
     * @param $lang string language shortname
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getChangeLanguage(Request $request, string $locale, string $lang)
    {
        $newLocale = in_array($lang, config('custom.availableLocales'), true)
            ? $lang
            : config('app.locale');

        $currentPath = $request->getPathInfo();
        
        $pathWithoutLocale = preg_replace('~^/' . preg_quote($locale) . '~', '', $currentPath);
        $pathWithoutChangeLanguage = preg_replace('~^/change-language/[a-z]{2}$~', '', $pathWithoutLocale);
        
        if ($pathWithoutChangeLanguage === $pathWithoutLocale) {
            $newPath = '/' . $newLocale;
        } else {
            $newPath = '/' . $newLocale . $pathWithoutChangeLanguage;
        }

        return redirect($newPath);
    }
}
