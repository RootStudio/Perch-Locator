<?php

include PERCH_PATH . '/addons/apps/jw_stockists/libraries/GoogleMapsGeocoder/GoogleMapsGeocoder.php';

class JwStockists_Geocode
{
    public static function geocode($address)
    {
        $Geocoder = new GoogleMapsGeocoder($address);
        return $Geocoder->geocode();
    }
}
