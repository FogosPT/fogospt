<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
use Illuminate\Http\Response;


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
        $meteo = LegacyApi::getMeteoByFire($this->fire['lat'], $this->fire['lng']);

        if(isset($meteo['wind']['deg'])){
            $meteo['wind']['deg'] = HelperFuncs::wind_cardinals($meteo['wind']['deg']);
        }

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $this->fire['meteo'] = $meteo;

        $ch = curl_init();
        $method_request = 'GET';
        curl_setopt($ch, CURLOPT_URL, "https://files.sonnyt.com/tweetie/v3/?type=hashtag&params[count]=10&params[q]=fogosPT".$id);
        // SSL important
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        curl_close($ch);


        $feed = json_decode($output);

        return view('index', array('fire' => $this->fire, 'feed'=>$feed, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCard($id)
    {
        $this->setFireById($id);
        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCard($id)
    {
        $this->setFireById($id);
        $status = LegacyApi::getStatusByFire($id);
        $this->fire['statusHistory'] = @$status['data'];

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


    public function getTwitterCard($id)
    {
        $this->setFireById($id);


        $ch = curl_init();
        $method_request = 'GET';
        curl_setopt($ch, CURLOPT_URL, "https://files.sonnyt.com/tweetie/v3/?type=hashtag&params[count]=10&params[q]=fogosPT".$id);
        // SSL important
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        curl_close($ch);


        $feed = json_decode($output);
        return view('elements.twitter', array('fire' => $this->fire, 'feed'=>$feed));
    }

    public function getAll()
    {
        return \Response::json(LegacyApi::getFires());
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

        if(isset($meteo['wind']['deg'])){
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
        $fire = LegacyApi::getFire($id);

        if(isset($fire['data'])){
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }
    }

    private function setMadeiraFireById($id)
    {
        $fire = LegacyApi::getMadeiraFire($id);

        if(isset($fire['data'])){
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }

    }
}
