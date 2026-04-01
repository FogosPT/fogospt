<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
use Illuminate\Http\Response;
use Jorenvh\Share\Share;


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

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $metadata = $this->generateMetadata();
        $shares = new Share();
        $s = $shares->page($metadata['url'])
            ->facebook()
            ->whatsapp();

        return view('index', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata));
    }

    public function getDetails($id)
    {
        if (!$id) {
            return view('index');
        }

        $this->setFireById($id);

        if(!$this->fire){
            abort(404);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFire($id);

        $this->fire['risk'] = @$risk['data'][0]['hoje'];
        if (isset($status['data'])) {
            $this->fire['statusHistory'] = $status['data'];
        } else {
            $this->fire['statusHistory'] = false;
        }

        $metadata = $this->generateMetadata();
        $shares = new Share();
        $s = $shares->page($metadata['url'])
            ->facebook()
            ->whatsapp();

        if(isset($this->fire['kml'])){
            $kml = preg_replace( "/\r|\n/", "", $this->fire['kml'] );
        } else {
            $kml = null;
        }

        if(isset($this->fire['kmlVost'])){
            $kmlVost = preg_replace( "/\r|\n/", "", $this->fire['kmlVost'] );
        } else {
            $kmlVost = null;
        }

        return view('detail', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata, 'kml' => $kml, 'kmlVost' => $kmlVost));
    }

    public function getSharesCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.shares', ['shares' => '', 'fire' => [], 'metadata' => []]);
        }

        $metadata = $this->generateMetadata();

        $shares = new Share();
        $s = $shares->page($metadata['url'], $metadata['title'])
            ->facebook()
            ->twitter()
            ->whatsapp();

        $s = str_replace('views/shares', 'fogo', $s);

        return view('elements.shares', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata));
    }

    public function getGeneralCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.risk', ['fire' => []]);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCard($locale, $id)
    {
        $this->setFireById($id);

        if ($this->fire === null) {
            return view('elements.status', ['fire' => []]);
        }

        $status = LegacyApi::getStatusByFire($id);
        $this->fire['statusHistory'] = $status['data'] ?? [];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCard($locale, $id)
    {
        $this->setFireById($id);

        return view('elements.meteo', array('fire' => $this->fire ?? []));
    }

    public function getExtraCard($locale, $id)
    {
        $this->setFireById($id);

        if (!empty($this->fire['extra']) || !empty($this->fire['pco']) || !empty($this->fire['cos'])) {
            return view('elements.extra', array('fire' => $this->fire));
        } else {
            return \Response::json();
        }

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

        if ($this->fire === null) {
            return view('index-madeira');
        }

        $risk = LegacyApi::getRiskByFire($id);
        $status = LegacyApi::getStatusByFireMadeira($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;
        $this->fire['statusHistory'] = $status['data'] ?? false;

        return view('index-madeira', array('fire' => $this->fire, 'metadata' => $this->generateMetadata()));
    }

    public function getGeneralCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        if ($this->fire === null) {
            return view('elements.risk', ['fire' => []]);
        }

        $risk = LegacyApi::getRiskByFire($id);
        $this->fire['risk'] = $risk['data'][0]['hoje'] ?? null;

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        if ($this->fire === null) {
            return view('elements.status', ['fire' => []]);
        }

        $status = LegacyApi::getStatusByFireMadeira($id);
        $this->fire['statusHistory'] = $status['data'] ?? [];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCardMadeira($locale, $id)
    {
        $this->setMadeiraFireById($id);

        return view('elements.meteo', array('fire' => $this->fire ?? []));
    }

    public function getExtraCardMadeira($locale, $id)
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

        if (isset($fire['data'])) {
            $this->fire = $fire['data'];
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

    public function getLightnings()
    {
        return [];
        $json = file_get_contents('https://www.ipma.pt/resources.www/transf/dea/dea.json');
        return $json;
    }


}
