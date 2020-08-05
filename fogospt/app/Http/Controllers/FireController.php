<?php

namespace App\Http\Controllers;

use App\Libs\Enums\FogosApiEndpoints;
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
        $risk = LegacyApi::getResultById(FogosApiEndpoints::GET_RISK_BY_FIRE, $id);
        $status = LegacyApi::getResultById(FogosApiEndpoints::GET_STATUS_BY_FIRE,$id);
        $meteo = OpenWeatherClient::getMeteoByFire($this->fire['lat'], $this->fire['lng']);

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
        $risk = LegacyApi::getResultById(FogosApiEndpoints::GET_RISK_BY_FIRE, $id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCard($id)
    {
        $this->setFireById($id);
        $status = LegacyApi::getResultById(FogosApiEndpoints::GET_STATUS_BY_FIRE,$id);
        $this->fire['statusHistory'] = @$status['data'];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCard($id)
    {
        $this->setFireById($id);
        $meteo = OpenWeatherClient::getMeteoByFire($this->fire['lat'], $this->fire['lng']);
        if (isset($meteo['wind']['deg'])) {
            $meteo['wind']['deg'] = HelperFuncs::wind_cardinals($meteo['wind']['deg']);
        }
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
        return \Response::json(LegacyApi::getResults(FogosApiEndpoints::NEW_FIRES));
    }

    public function getMadeira($id)
    {
        if (!$id) {
            return view('index-madeira');
        }

        $this->setMadeiraFireById($id);
        $risk = LegacyApi::getResultById(FogosApiEndpoints::GET_RISK_BY_FIRE, $id);
        $status = LegacyApi::getResultById(FogosApiEndpoints::GET_STATUS_BY_MADEIRA_FIRE,$id);
        $meteo = OpenWeatherClient::getMeteoByFire($this->fire['lat'], $this->fire['lng']);

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
        $risk = LegacyApi::getResultById(FogosApiEndpoints::GET_RISK_BY_FIRE, $id);
        $this->fire['risk'] = @$risk['data'][0]['hoje'];

        return view('elements.risk', array('fire' => $this->fire));
    }

    public function getStatusCardMadeira($id)
    {
        $this->setMadeiraFireById($id);
        $status = LegacyApi::getResultById(FogosApiEndpoints::GET_STATUS_BY_MADEIRA_FIRE, $id);
        $this->fire['statusHistory'] = @$status['data'];

        return view('elements.status', array('fire' => $this->fire));
    }

    public function getMeteoCardMadeira($id)
    {
        $this->setMadeiraFireById($id);
        $meteo = OpenWeatherClient::getMeteoByFire($this->fire['lat'], $this->fire['lng']);
        if (isset($meteo['wind']['deg'])) {
            $meteo['wind']['deg'] = HelperFuncs::wind_cardinals($meteo['wind']['deg']);
        }
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
        return \Response::json(LegacyApi::getResults(FogosApiEndpoints::NEW_FIRES));
    }


    private function setFireById($id)
    {
        $fire = LegacyApi::getResultById(FogosApiEndpoints::GET_FIRE, $id);

        if (isset($fire['data'])) {
            $this->fire = $fire['data'];
        } else {
            $this->fire = null;
        }
    }

    private function setMadeiraFireById($id)
    {
        $fire = LegacyApi::getResultById(FogosApiEndpoints::GET_MADEIRA_FIRE, $id);

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
                return json_decode($exists);
            } else {
                $requestMethod = 'GET';
                $getfield = "?q=#IR{$hashtag}&result_type=recent";

                $settings = array(
                    'oauth_access_token' => ENV('TWITTER_OAUTH_ACCESS_TOKEN'),
                    'oauth_access_token_secret' => ENV('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'),
                    'consumer_key' => env('TWITTER_CONSUMER_KEY'),
                    'consumer_secret' => env('TWITTER_CONSUMER_SECRET')
                );


                $twitter = new \TwitterAPIExchange($settings);
                $result = $twitter->setGetfield($getfield)
                    ->buildOauth(env('TWITTER_SEARCH_TWEETS_ENDPOINT'), $requestMethod)
                    ->performRequest();

                $feed = array();

                if ($result) {
                    $feed = json_decode($result);
                    if (isset($feed->statuses)) {
                        $feed = $feed->statuses;
                    }
                }


                if(!empty($feed)){
                    Redis::set('twitter:'. $hashtag, json_encode($feed),'EX', 1080);
                }

                return $feed;
            }
        } else {
            $requestMethod = 'GET';
            $getfield = "?q=#IR{$hashtag}&result_type=recent";

            $settings = array(
                'oauth_access_token' => ENV('TWITTER_OAUTH_ACCESS_TOKEN'),
                'oauth_access_token_secret' => ENV('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'),
                'consumer_key' => env('TWITTER_CONSUMER_KEY'),
                'consumer_secret' => env('TWITTER_CONSUMER_SECRET')
            );


            $twitter = new \TwitterAPIExchange($settings);
            $result = $twitter->setGetfield($getfield)
                ->buildOauth(env('TWITTER_SEARCH_TWEETS_ENDPOINT'), $requestMethod)
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
        $json = file_get_contents(env('GET_LIGHTNINGS_ENDPOINT'));
        return $json;
    }


}
