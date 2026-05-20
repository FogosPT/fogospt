<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;
use Illuminate\Http\Request;

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
            return \Response::json(['success' => false, 'error' => 'invalid_params'], 400);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . env('FIREBASE_TOKEN'),
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, []);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return \Response::json(['success' => $httpcode === 200, 'topic' => $topic]);
    }

    public function unsubscribe(Request $request)
    {
        $token = $request->get('token');
        $topic = $this->resolveTopic($request->get('topic'));

        if (!$token || !$topic) {
            return \Response::json(['success' => false, 'error' => 'invalid_params'], 400);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://iid.googleapis.com/iid/v1:batchRemove");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . env('FIREBASE_TOKEN'),
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'to' => "/topics/{$topic}",
            'registration_tokens' => [$token],
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return \Response::json(['success' => $httpcode === 200, 'topic' => $topic]);
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
