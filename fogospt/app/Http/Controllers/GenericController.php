<?php

namespace App\Http\Controllers;

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
        $this->setPageName('');
        return view('index')->with(['metadata' => $this->generateMetadata()]);
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

    public function getPartnerships() {
	    $this->setPageName(__('includes.menu.partnerships'));
	    return view('partnerships')->with(['metadata' => $this->generateMetadata()]);
    }

	/**
	 * Sets the app language based on user action
	 * in the case user try to set the app language
	 * to a language that we dont have a record as supported language
	 * it will fallback for the default app locale
	 *
	 * @param $lang string language shortname
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
    public function getChangeLanguage($lang) {
	    if (in_array($lang, config('custom.availableLocales'))) {
		    session(['userLocale' => $lang]);
	    } else {
		    session(['userLocale' => config('app.locale')]);
	    }
	    return redirect()->back();
    }
}
