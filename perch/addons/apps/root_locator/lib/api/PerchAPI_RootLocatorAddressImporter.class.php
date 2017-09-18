<?php

/**
 * Class PerchAPI_RootLocatorAddressImporter
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class PerchAPI_RootLocatorAddressImporter extends PerchAPI_ContentImporter
{
    protected function setup()
    {
        $API = new PerchAPI(1.0, 'root_locator');
        $this->set_factory(new RootLocator_Addresses($API));

        $Template = $API->get('Template');
        $Template->set('locator/address.html', 'locator');

        $this->set_template($Template);
    }

    protected function validate_input($data)
    {
        if(!isset($data['addressTitle']) || trim($data['addressTitle']) === '') {
            throw new \Exception('Missing property: addressTitle');
        }

        if(!isset($data['addressStreet']) || trim($data['addressStreet']) === '') {
            throw new \Exception('Missing property: addressStreet');
        }

        if(!isset($data['addressPostcode']) || trim($data['addressPostcode']) === '') {
            throw new \Exception('Missing property: addressPostcode');
        }

        parent::validate_input($data);
    }
}
