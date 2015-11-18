<?php

include PERCH_PATH . '/addons/apps/jw_stockists/libraries/GoogleMapsGeocoder/GoogleMapsGeocoder.php';

/**
 * Class JwStockists_Geocode
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Geocode
{
    /**
     * Geocode address to lat,lng with optional API key
     *
     * @param $address
     * @return array|SimpleXMLElement|string
     */
    public static function geocode($address)
    {
        $API = new PerchAPI(1.0, 'jw_stockists');
        $Settings = $API->get('Settings');
        $api_key = $Settings->get('jw_stockists_google_api_key')->val();

        $Geocoder = new GoogleMapsGeocoder($address);

        if($api_key) {
            $Geocoder->setApiKey($api_key);
        }

        return $Geocoder->geocode();
    }
}
