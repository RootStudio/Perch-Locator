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
     * @var null|string
     */
    private $errorKey;

    /**
     * @var bool|string
     */
    private $error = false;

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
        try {

            $this->addresses = $this->geocoder->limit($limit)->geocode($address);

        } catch (\Geocoder\Exception\NoResult $ex) {

            $this->errorKey = 'no_results';
            $this->error = RootLocator_Errors::noResults();
            PerchUtil::debug(sprintf('Locator: %s', $ex->getMessage()), 'error');

        } catch (\Geocoder\Exception\HttpError $ex) {

            $this->errorKey = 'http_error';
            $this->error = RootLocator_Errors::httpError();
            PerchUtil::debug(sprintf('Locator: %s', $ex->getMessage()), 'error');

        } catch (\Geocoder\Exception\InvalidCredentials $ex) {

            $this->errorKey = 'invalid_credentials';
            $this->error = RootLocator_Errors::invalidCredentials();
            PerchUtil::debug(sprintf('Locator: %s', $ex->getMessage()), 'error');

        } catch (\Geocoder\Exception\QuotaExceeded $ex) {

            $this->errorKey = 'quota_exceeded';
            $this->error = RootLocator_Errors::quotaExceeded();
            PerchUtil::debug(sprintf('Locator: %s', $ex->getMessage()), 'error');

        } catch (Exception $ex) {

            $this->errorKey = 'unknown';
            $this->error = RootLocator_Errors::unknown();
            PerchUtil::debug(sprintf('Locator: %s', $ex->getMessage()), 'error');

        }

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

        if (!$this->hasError()) {
            foreach ($this->addresses->all() as $address) {
                $addresses[] = $address->toArray();
            }
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
        if ($this->hasError()) {
            return false;
        }

        return $this->addresses->first()->toArray();
    }

    /**
     * Return first address coordinates
     *
     * @return mixed
     */
    public function getFirstCoordinates()
    {
        if ($this->hasError()) {
            return false;
        }

        $coordinates = $this->addresses->first()->getCoordinates();

        return [
            'latitude'  => $coordinates->getLatitude(),
            'longitude' => $coordinates->getLongitude()
        ];
    }

    /**
     * Return status of geocode operation
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->error !== false;
    }

    /**
     * Return the error key
     *
     * @return null|string
     */
    public function getErrorKey()
    {
        return $this->errorKey;
    }

    /**
     * Return the error message
     *
     * @return bool|string
     */
    public function getErrorMessage()
    {
        return $this->error;
    }
}
