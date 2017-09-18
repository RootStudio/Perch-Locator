<?php

/**
 * Class PerchFieldType_locator
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class PerchFieldType_locator extends PerchAPI_FieldType
{
    /**
     * Output form fields for admin edit page
     *
     * @param array $details
     *
     * @return string
     */
    public function render_inputs($details = [])
    {
        $Settings = PerchSettings::fetch();

        if (!$Settings->get('root_locator_google_api_key')->val()) {
            return '<p class="help">Please install the Root Locator app and add a Google API Key.</p>';
        }

        $id = $this->Tag->input_id();
        $val = '';

        if (isset($details[$id]) && $details[$id] != '') {
            $json = $details[$id];
            $val = $json['id'];
        }

        if (!class_exists('RootLocator_Addresses')) {
            include_once(PERCH_PATH . '/addons/apps/root_locator/lib/RootLocatorAddresses.class.php');
            include_once(PERCH_PATH . '/addons/apps/root_locator/lib/RootLocatorAddress.class.php');
        }

        $Addresses = new RootLocator_Addresses();
        $addresses = $Addresses->getCompleted();

        $options = [];

        if (PerchUtil::count($addresses)) {
            $options = array_map(function ($address) {
                return [
                    'label' => $address->addressTitle(),
                    'value' => $address->id(),
                ];
            }, $addresses);
        }

        if (PerchUtil::count($options)) {
            $s = $this->Form->select($id, $options, $val);
        } else {
            $s = '-';
        }

        return $s;
    }

    /**
     * Read in form input and format for database storage
     *
     * @param bool $post
     * @param bool $Item
     *
     * @return array
     */
    public function get_raw($post = false, $Item = false)
    {
        $store = [];
        $id = $this->Tag->id();

        if ($post === false) $post = $_POST;

        if (isset($post[$id])) {
            $this->raw_item = trim($post[$id]);

            if (!class_exists('RootLocator_Addresses')) {
                include_once(PERCH_PATH . '/addons/apps/root_locator/lib/RootLocatorAddresses.class.php');
                include_once(PERCH_PATH . '/addons/apps/root_locator/lib/RootLocatorAddress.class.php');
            }

            $Addresses = new RootLocator_Addresses();
            $address = $Addresses->find($this->raw_item);

            if (is_object($address)) {
                $data = $address->to_array();
                $blacklist = ['addressDynamicFields', 'perch_categories', 'categories'];

                foreach ($data as $key => $value) {
                    if (in_array($key, $blacklist)) continue;

                    $key = str_replace('address', '', $key);
                    $store[strtolower($key)] = $value;
                }

                $store['_default'] = $store['title'];
            }
        }

        return $store;
    }

    /**
     * Process raw input ready for templating
     *
     * @param bool $raw
     *
     * @return bool|mixed
     */
    public function get_processed($raw = false)
    {
        if (is_array($raw) && isset($raw['title'])) {
            if ($this->Tag->output()) {
                if (isset($raw[$this->Tag->output()])) {
                    return $raw[$this->Tag->output()];
                }
            }

            return $raw['title'];
        }

        return $raw;
    }

    /**
     * Return value to be used for searching
     *
     * @param bool $raw
     *
     * @return bool|mixed
     */
    public function get_search_text($raw = false)
    {
        if ($raw === false) $raw = $this->get_raw();
        if (!PerchUtil::count($raw)) return false;

        if (isset($raw['title'])) return $raw['title'];

        return false;
    }

    /**
     * Render preview in admin listing view
     *
     * @param bool $raw
     *
     * @return string
     */
    public function render_admin_listing($raw = false)
    {
        return $this->get_processed($raw);
    }
}
