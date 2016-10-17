<?php

/**
 * Class RootLocator_Address
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Address extends PerchAPI_Base
{
    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'root_locator_addresses';
    /**
     * Primary key
     *
     * @var string
     */
    protected $pk = 'addressID';
    /**
     * Modified date column
     *
     * @var string
     */
    protected $modified_date_column = 'addressUpdated';

    public function fullAddress()
    {
        $address = [
            $this->addressBuilding(),
            $this->addressStreet(),
            $this->addressTown(),
            $this->addressRegion(),
            $this->addressPostcode(),
            $this->addressCountry()
        ];

        return implode(', ', $address);
    }

    public function geocode()
    {
        $Geocoder = RootLocator_GeocoderFactory::createGeocoder();
        $result = $Geocoder->geocode($this->fullAddress());

        if ($result->hasError()) {
            return $this->update([
                'addressError' => $result->getErrorKey()
            ]);
        }

        $coordinates = $result->getFirstCoordinates();

        return $this->update([
            'addressLatitude'  => $coordinates['latitude'],
            'addressLongitude' => $coordinates['longitude'],
            'addressError'     => null
        ]);
    }

    public function update($data, $geocode = false)
    {
        $result = parent::update($data);

        if ($geocode) {
            $result = $this->geocode();
        }

        return $result;
    }

    public function hasCoordinates()
    {
        return ($this->addressLatitude() && $this->addressLongitude());
    }

    public function hasError()
    {
        return !is_null($this->addressError());
    }

    public function addAttempt()
    {

    }

    public function clearAttempts()
    {

    }

    public function preview()
    {
        $PerchSettings = PerchSettings::fetch();
        $apiKey = $PerchSettings->get('root_locator_google_api_key')->settingValue();

        if (!$this->hasCoordinates() || !$apiKey) {
            return false;
        }

        $base_url = 'https://maps.googleapis.com/maps/api/staticmap?';
        $parameters = [
            'size'    => '400x200',
            'maptype' => 'roadmap',
            'markers' => 'color:red|' . $this->addressLatitude() . ',' . $this->addressLongitude(),
            'key'     => $apiKey
        ];

        return '<img class="map-preview" src="' . $base_url . http_build_query($parameters) . '" />';
    }
}
