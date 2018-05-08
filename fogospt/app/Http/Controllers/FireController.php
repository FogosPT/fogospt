<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;

class FireController extends Controller
{
    public $fire;

    public function get($id)
    {
        if (!$id) {
            return view('index');
        }

        $this->setFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);
        $fire['data']['risk'] = $risk['data'][0]['hoje'];
        $fire['data']['status'] = $status['data'];

        return view('index', array('fire' => $fire, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCard($id)
    {
        $this->setFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $fire['data']['risk'] = $risk['data'][0]['hoje'];

        return view('elements.risk', array('fire' => $fire));
    }

    public function getStatusCard($id)
    {
        $this->setFireById($id);
        $status = LegacyApi::getStatusByFire($id);
        $fire['data']['status'] = $status['data'];

        return view('elements.status', array('fire' => $fire));
    }

    private function setFireById($id)
    {
        $this->fire = LegacyApi::getFire($id)['data'];
    }
}
