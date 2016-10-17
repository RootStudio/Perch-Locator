<?php

use Geocoder\Provider\Provider;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;

class RootLocator_GeocoderFactory
{
    public static function createGeocoder()
    {
        $PerchSettings = PerchSettings::fetch();

        $apiKey = $PerchSettings->get('root_locator_google_api_key')->settingValue();
        $httpAdapter = self::createHttpAdaptor();

        $provider = new GoogleMaps(
            $httpAdapter,
            null,
            null,
            false,
            $apiKey
        );

        return self::createGeocoderLibrary($provider);
    }

    protected static function createHttpAdaptor($config = [])
    {
        return new CurlHttpAdapter();
    }

    protected static function createGeocoderLibrary(Provider $provider)
    {
        return new RootLocator_Geocoder($provider);
    }
}
