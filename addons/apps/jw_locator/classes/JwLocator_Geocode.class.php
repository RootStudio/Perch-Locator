<?php

include PERCH_PATH . '/addons/apps/jw_locator/libraries/GoogleMapsGeocoder/GoogleMapsGeocoder.php';

/**
 * Class JwLocator_Geocode
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Geocode
{
    /**
     * Geocode address to lat,lng with optional API key
     *
     * @param $address
     * @return array|SimpleXMLElement|string
     */
    public static function geocode($address)
    {
        $API = new PerchAPI(1.0, 'jw_locator');
        $Settings = $API->get('Settings');
        $api_key = $Settings->get('jw_locator_google_api_key')->val();

        $Geocoder = new GoogleMapsGeocoder($address);

        if($api_key) {
            $Geocoder->setApiKey($api_key);
        }

        return $Geocoder->geocode();
    }
}
