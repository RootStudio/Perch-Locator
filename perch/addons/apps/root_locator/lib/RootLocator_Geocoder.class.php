<?php

use Geocoder\Provider\Provider;

/**
 * Class RootLocator_Geocoder
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Geocoder
{
    /**
     * @var Provider
     */
    private $geocoder;

    /**
     * @var \Geocoder\Model\AddressCollection
     */
    private $addresses;

    /**
     * RootLocator_Geocoder constructor.
     *
     * @param Provider $geocoder
     */
    public function __construct(Provider $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @param string $address
     * @param int    $limit
     *
     * @return $this
     */
    public function geocode($address, $limit = 1)
    {
        $this->addresses = $this->geocoder->limit($limit)->geocode($address);

        return $this;
    }

    /**
     * Return collection of addresses
     *
     * @return array
     */
    public function getAddresses()
    {
        $addresses = [];

        foreach ($this->addresses->all() as $address) {
            $addresses[] = $address->toArray();
        }

        return $addresses;
    }

    /**
     * Return first address
     *
     * @return array
     */
    public function getFirstAddress()
    {
        return $this->addresses->first()->toArray();
    }

    /**
     * Return first address coordinates
     *
     * @return mixed
     */
    public function getFirstCoordinates()
    {
        $coordinates = $this->addresses->first()->getCoordinates();

        return [
            'latitude'  => $coordinates->getLatitude(),
            'longitude' => $coordinates->getLongitude()
        ];
    }
}
