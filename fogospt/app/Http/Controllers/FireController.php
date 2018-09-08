<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
use Illuminate\Http\Response;
use App\Models\Fire;
use App\Models\HistoryStatus;
use App\Models\History;
use App\Models\HistoryDanger;

class FireController extends Controller
{
    public $fire;

    public function get($id)
    {
        if (!$id) {
            return view('index');
        }

        $this->setFireById($id);
        $risk = HistoryDanger::getByCounty($this->fire->concelho);
        $status = HistoryStatus::getLastRecordsById($id);
        $meteo = LegacyApi::getMeteoByFire($this->fire->lat, $this->fire->lng);
        if (isset($meteo['wind']['deg'])) {
            $meteo['wind']['deg'] = HelperFuncs::wind_cardinals($meteo['wind']['deg']);
        }
        $this->fire['risk'] = $risk->hoje;
        if (!empty($status)) {
            $this->fire['statusHistory'] = $status;
        } else {
            $this->fire['statusHistory'] = false;
        }

        $this->fire['meteo'] = $meteo;

        return view('index', array('fire' => $this->fire, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCard($id)
    {
        $this->setFireById($id);
        $risk = HistoryDanger::getByCounty($this->fire->concelho);
        $this->fire['risk'] = $risk->hoje;
        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCard($id)
    {
        $this->setFireById($id);
        $status = HistoryStatus::getLastRecordsById($id);
        $this->fire['statusHistory'] = $status;
        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCard($id)
    {
        $this->setFireById($id);
        $meteo = LegacyApi::getMeteoByFire($this->fire['lat'], $this->fire['lng']);
        $this->fire['meteo'] = $meteo;

        return view('elements.meteo', array('fire' => $this->fire));
    }

    public function getExtraCard($id)
    {
        $this->setFireById($id);

        if (!empty($this->fire['extra'])) {
            return view('elements.extra', array('fire' => $this->fire));
        } else {
            return \Response::json();
        }
    }

    public function getAll()
    {
        return Fire::getAll();
    }

    public function getMadeira($id)
    {
        if (!$id) {
            return view('index-madeira');
        }

        $this->setMadeiraFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFireMadeira($id);
        $meteo = LegacyApi::getMeteoByFire($this->fire['lat'], $this->fire['lng']);

        if (isset($meteo['wind']['deg'])) {
            $meteo['wind']['deg'] = HelperFuncs::wind_cardinals($meteo['wind']['deg']);
        }

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $this->fire['meteo'] = $meteo;

        return view('index-madeira', array('fire' => $this->fire, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCardMadeira($id)
    {
        $this->setMadeiraFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCardMadeira($id)
    {
        $this->setMadeiraFireById($id);
        $status = LegacyApi::getStatusByFireMadeira($id);
        $this->fire['statusHistory'] = @$status['data'];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCardMadeira($id)
    {
        $this->setMadeiraFireById($id);
        $meteo = LegacyApi::getMeteoByFire($this->fire['lat'], $this->fire['lng']);
        $this->fire['meteo'] = $meteo;

        return view('elements.meteo', array('fire' => $this->fire));
    }

    public function getExtraCardMadeira($id)
    {
        $this->setMadeiraFireById($id);

        if (!empty($this->fire['extra'])) {
            return view('elements.extra', array('fire' => $this->fire));
        } else {
            return \Response::json();
        }
    }

    public function getAllMadeira()
    {
        return \Response::json(LegacyApi::getFires());
    }


    private function setFireById($id)
    {
        $fire = Fire::getFire($id);
        if (!empty($fire)) {
            $this->fire = $fire;
        } else {
            $this->fire = null;
        }
    }

    private function setMadeiraFireById($id)
    {
        $fire = LegacyApi::getMadeiraFire($id);

        if (isset($fire['data'])) {
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }
    }
}
