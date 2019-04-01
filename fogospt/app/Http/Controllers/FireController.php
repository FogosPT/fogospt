<?php

namespace App\Http\Controllers;

use App\Libs\HelperFuncs;
use App\Libs\LegacyApi;
use Illuminate\Http\Response;
use Jorenvh\Share\Share;
use Illuminate\Support\Facades\Redis;


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

        $feed = array();

        if (isset($this->fire['concelho'])) {
          $feed = $this->getTwitter($this->fire['concelho']);
        }

        $metadata = $this->generateMetadata();
        $shares = new Share();
        $s = $shares->page($metadata['url'])
            ->facebook()
            ->twitter()
            ->whatsapp();


        return view('index', array('shares' => $s, 'fire' => $this->fire, 'feed' => $feed, 'metadata' => $metadata));
    }

    public function getSharesCard($id)
    {
        $this->setFireById($id);
        $metadata = $this->generateMetadata();
        $shares = new Share();
        $s = $shares->page($metadata['url'], $metadata['title'])
            ->facebook()
            ->twitter()
            ->whatsapp();

        $s = str_replace('views/shares', 'fogo', $s);

        return view('elements.shares', array('shares' => $s, 'fire' => $this->fire, 'metadata' => $metadata));
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

        $feed = $this->getTwitter($this->fire['concelho']);

        return view('elements.twitter', array('fire' => $this->fire, 'feed' => $feed));
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

    private function getTwitter($concelho)
    {
        $hashtag = preg_replace('/\s+/', '', $concelho);

        if(env('APP_ENV') === 'production'){
            $exists = Redis::get('twitter:'. $hashtag);
            if($exists){
                return json_decode($exists,true);
            } else {
                $url = 'https://api.twitter.com/1.1/search/tweets.json';
                $requestMethod = 'GET';
                $getfield = "?q=#IF{$hashtag}&result_type=recent";

                $settings = array(
                    'oauth_access_token' => ENV('TWITTER_OAUTH_ACCESS_TOKEN'),
                    'oauth_access_token_secret' => ENV('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'),
                    'consumer_key' => env('TWITTER_CONSUMER_KEY'),
                    'consumer_secret' => env('TWITTER_CONSUMER_SECRET')
                );


                $twitter = new \TwitterAPIExchange($settings);
                $result = $twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest();

                $feed = array();

                if ($result) {
                    $feed = json_decode($result);
                    if (isset($feed->statuses)) {
                        $feed = $feed->statuses;
                    }
                }


                if(!empty($feed)){
                    Redis::set('twitter:'. $hashtag, json_encode($result),'EX', 1080);
                }

                return $feed;
            }
        } else {
            $url = 'https://api.twitter.com/1.1/search/tweets.json';
            $requestMethod = 'GET';
            $getfield = "?q=#IF{$hashtag}&result_type=recent";

            $settings = array(
                'oauth_access_token' => ENV('TWITTER_OAUTH_ACCESS_TOKEN'),
                'oauth_access_token_secret' => ENV('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'),
                'consumer_key' => env('TWITTER_CONSUMER_KEY'),
                'consumer_secret' => env('TWITTER_CONSUMER_SECRET')
            );


            $twitter = new \TwitterAPIExchange($settings);
            $result = $twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();

            $feed = array();

            if ($result) {
                $feed = json_decode($result);
                if (isset($feed->statuses)) {
                    $feed = $feed->statuses;
                }
            }

            return $feed;

        }
    }


    public function getLightnings()
    {
        $json = file_get_contents('https://www.ipma.pt/resources.www/transf/dea/dea.json');
        return $json;
    }


}
