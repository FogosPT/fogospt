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
        $this->setPageName('Sobre');
        return view('about')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getInformation()
    {
        $this->setPageName('Informação');
        return view('information')->with(['metadata' => $this->generateMetadata()]);
    }

    public function getManifest()
    {
        $this->setPageName('Manifesto');
        return view('manifest')->with(['metadata' => $this->generateMetadata()]);
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
