<?php
/**
 * Created by PhpStorm.
 * User: tomahock
 * Date: 03/05/2018
 * Time: 19:08
 */

namespace App\Libs;

use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redis;
use Location\Coordinate;
use Location\Polygon;


class HotSpots
{
    public static function getModis()
    {
        $result = self::_getModis();
        return $result;
    }

    public static function getVIIRS()
    {
        $result = self::_getVIIRS();
        return $result;
    }

    private static function _getModis()
    {
        $final = [];

        $geofence = new Polygon();

        $geofence->addPoint(new Coordinate(41.89318, -8.890156));
        $geofence->addPoint(new Coordinate(42.10138, -8.579793));
        $geofence->addPoint(new Coordinate(42.17267, -8.176045));
        $geofence->addPoint(new Coordinate(42.03818, -8.052449));
        $geofence->addPoint(new Coordinate(41.9034, -8.178792));
        $geofence->addPoint(new Coordinate(41.82363, -8.110127));
        $geofence->addPoint(new Coordinate(41.9504, -7.904134));
        $geofence->addPoint(new Coordinate(41.87887, -7.283406));
        $geofence->addPoint(new Coordinate(42.01778, -7.168050));
        $geofence->addPoint(new Coordinate(41.98611, -6.533113));
        $geofence->addPoint(new Coordinate(41.83895, -6.491914));
        $geofence->addPoint(new Coordinate(41.69145, -6.511140));
        $geofence->addPoint(new Coordinate(41.6853, -6.305147));
        $geofence->addPoint(new Coordinate(41.57444, -6.151338));
        $geofence->addPoint(new Coordinate(41.35215, -6.326688));
        $geofence->addPoint(new Coordinate(41.10013, -6.741422));
        $geofence->addPoint(new Coordinate(40.94057, -6.834806));
        $geofence->addPoint(new Coordinate(40.86791, -6.768888));
        $geofence->addPoint(new Coordinate(40.34044, -6.771259));
        $geofence->addPoint(new Coordinate(40.2448, -6.875931));
        $geofence->addPoint(new Coordinate(40.17978, -6.996781));
        $geofence->addPoint(new Coordinate(40.06847, -6.853959));
        $geofence->addPoint(new Coordinate(39.88115, -6.867692));
        $geofence->addPoint(new Coordinate(39.6468, -7.016007));
        $geofence->addPoint(new Coordinate(39.62988, -7.458207));
        $geofence->addPoint(new Coordinate(39.46256, -7.271439));
        $geofence->addPoint(new Coordinate(39.03517, -6.913092));
        $geofence->addPoint(new Coordinate(38.86215, -7.020208));
        $geofence->addPoint(new Coordinate(38.67156, -7.242682));
        $geofence->addPoint(new Coordinate(38.43959, -7.278387));
        $geofence->addPoint(new Coordinate(38.21551, -7.072394));
        $geofence->addPoint(new Coordinate(38.23924, -6.913092));
        $geofence->addPoint(new Coordinate(37.99471, -6.983213));
        $geofence->addPoint(new Coordinate(37.94924, -7.216672));
        $geofence->addPoint(new Coordinate(37.82134, -7.277097));
        $geofence->addPoint(new Coordinate(37.72147, -7.397947));
        $geofence->addPoint(new Coordinate(37.55182, -7.477598));
        $geofence->addPoint(new Coordinate(37.15665, -7.362241));
        $geofence->addPoint(new Coordinate(32.17161, -16.69045));
        $geofence->addPoint(new Coordinate(39.329, -34.15449));
        $geofence->addPoint(new Coordinate(40.87512, -34.04463));


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://firms.modaps.eosdis.nasa.gov/data/active_fire/c6/csv/MODIS_C6_Europe_24h.csv",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: firms.modaps.eosdis.nasa.gov",
                "Postman-Token: f3349609-b9bb-4109-983f-8b3ac91742e3,ab836358-57bf-49a1-ac1b-da45dc8cdac1",
                "User-Agent: PostmanRuntime/7.15.0",
                "accept-encoding: gzip, deflate",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode([]);
        } else {
            $flat_array = array_map("str_getcsv", explode("\n", $response));
            $obj = NULL;

            $data = $flat_array;
            $columns = $data[0];

            $json = [];
            foreach ($data as $row_index => $row_data) {
                if($row_index === 0) continue;
                $json[$row_index] = [];
                foreach ($row_data as $column_index => $column_value) {
                    if(isset($columns[$column_index])){
                        $label = $columns[$column_index];
                        $json[$row_index][$label] = $column_value;
                    }
                }
            }

            $aux = json_decode(json_encode($json), true);

            foreach ($aux as $linha){
                if(isset($linha["latitude"]) && isset($linha["longitude"])) {
                    $inside = new Coordinate(floatval($linha["latitude"]), floatval($linha["longitude"]));
                    if($geofence->contains($inside))
                        array_push($final, $linha);
                }
            }

            return $json;
        }
    }

    private static function _getVIIRS()
    {
        $final = [];

        $geofence = new Polygon();

        $geofence->addPoint(new Coordinate(41.89318, -8.890156));
        $geofence->addPoint(new Coordinate(42.10138, -8.579793));
        $geofence->addPoint(new Coordinate(42.17267, -8.176045));
        $geofence->addPoint(new Coordinate(42.03818, -8.052449));
        $geofence->addPoint(new Coordinate(41.9034, -8.178792));
        $geofence->addPoint(new Coordinate(41.82363, -8.110127));
        $geofence->addPoint(new Coordinate(41.9504, -7.904134));
        $geofence->addPoint(new Coordinate(41.87887, -7.283406));
        $geofence->addPoint(new Coordinate(42.01778, -7.168050));
        $geofence->addPoint(new Coordinate(41.98611, -6.533113));
        $geofence->addPoint(new Coordinate(41.83895, -6.491914));
        $geofence->addPoint(new Coordinate(41.69145, -6.511140));
        $geofence->addPoint(new Coordinate(41.6853, -6.305147));
        $geofence->addPoint(new Coordinate(41.57444, -6.151338));
        $geofence->addPoint(new Coordinate(41.35215, -6.326688));
        $geofence->addPoint(new Coordinate(41.10013, -6.741422));
        $geofence->addPoint(new Coordinate(40.94057, -6.834806));
        $geofence->addPoint(new Coordinate(40.86791, -6.768888));
        $geofence->addPoint(new Coordinate(40.34044, -6.771259));
        $geofence->addPoint(new Coordinate(40.2448, -6.875931));
        $geofence->addPoint(new Coordinate(40.17978, -6.996781));
        $geofence->addPoint(new Coordinate(40.06847, -6.853959));
        $geofence->addPoint(new Coordinate(39.88115, -6.867692));
        $geofence->addPoint(new Coordinate(39.6468, -7.016007));
        $geofence->addPoint(new Coordinate(39.62988, -7.458207));
        $geofence->addPoint(new Coordinate(39.46256, -7.271439));
        $geofence->addPoint(new Coordinate(39.03517, -6.913092));
        $geofence->addPoint(new Coordinate(38.86215, -7.020208));
        $geofence->addPoint(new Coordinate(38.67156, -7.242682));
        $geofence->addPoint(new Coordinate(38.43959, -7.278387));
        $geofence->addPoint(new Coordinate(38.21551, -7.072394));
        $geofence->addPoint(new Coordinate(38.23924, -6.913092));
        $geofence->addPoint(new Coordinate(37.99471, -6.983213));
        $geofence->addPoint(new Coordinate(37.94924, -7.216672));
        $geofence->addPoint(new Coordinate(37.82134, -7.277097));
        $geofence->addPoint(new Coordinate(37.72147, -7.397947));
        $geofence->addPoint(new Coordinate(37.55182, -7.477598));
        $geofence->addPoint(new Coordinate(37.15665, -7.362241));
        $geofence->addPoint(new Coordinate(32.17161, -16.69045));
        $geofence->addPoint(new Coordinate(39.329, -34.15449));
        $geofence->addPoint(new Coordinate(40.87512, -34.04463));


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://firms.modaps.eosdis.nasa.gov/data/active_fire/noaa-20-viirs-c2/csv/J1_VIIRS_C2_Europe_24h.csv",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: firms.modaps.eosdis.nasa.gov",
                "Postman-Token: f3349609-b9bb-4109-983f-8b3ac91742e3,ab836358-57bf-49a1-ac1b-da45dc8cdac1",
                "User-Agent: PostmanRuntime/7.15.0",
                "accept-encoding: gzip, deflate",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode([]);
        } else {
            $flat_array = array_map("str_getcsv", explode("\n", $response));
            $obj = NULL;

            $data = $flat_array;
            $columns = $data[0];

            $json = [];
            foreach ($data as $row_index => $row_data) {
                if($row_index === 0) continue;
                $json[$row_index] = [];
                foreach ($row_data as $column_index => $column_value) {
                    $label = $columns[$column_index];
                    $json[$row_index][$label] = $column_value;
                }
            }

            $aux = json_decode(json_encode($json), true);

            foreach ($aux as $linha){
                if($linha["latitude"] && $linha["longitude"]) {
                    $inside = new Coordinate(floatval($linha["latitude"]), floatval($linha["longitude"]));
                    if($geofence->contains($inside))
                        array_push($final, $linha);
                }
            }

            return $json;
        }
    }
}
