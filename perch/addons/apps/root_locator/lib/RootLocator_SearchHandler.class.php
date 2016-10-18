<?php

/**
 * Class RootLocator_SearchHandler
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_SearchHandler implements PerchAPI_SearchHandler
{
    /**
     * Output search SQL for Perch Runway
     *
     * @param string $key
     *
     * @return string
     */
    public static function get_admin_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'root_locator');
        $db = $API->get('DB');

        $sql = '
            SELECT \'' . __CLASS__ . '\' AS source, 
            MATCH(addressTitle, addressBuilding, addressStreet, addressPostcode) 
            AGAINST(' . $db->pdb($key) . ') AS score, addressTitle, addressBuilding, addressStreet, addressPostcode, addressID, "", "", ""
            FROM ' . PERCH_DB_PREFIX . 'root_locator_addresses
            WHERE MATCH(addressTitle, addressBuilding, addressStreet, addressPostcode) AGAINST(' . $db->pdb($key) . ')';

        return $sql;
    }

    /**
     * Output search SQL for runtime search queries
     *
     * @param string $key
     *
     * @return string
     */
    public static function get_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'root_locator');
        $db = $API->get('DB');

        $sql = '
            SELECT \'' . __CLASS__ . '\' AS source, 
            MATCH(addressTitle, addressBuilding, addressStreet, addressPostcode) 
            AGAINST(' . $db->pdb($key) . ') AS score, addressTitle, addressBuilding, addressStreet, addressPostcode, addressID, "", "", ""
            FROM ' . PERCH_DB_PREFIX . 'root_locator_addresses
            WHERE MATCH(addressTitle, addressBuilding, addressStreet, addressPostcode) AGAINST(' . $db->pdb($key) . ')
            AND addressLatitude IS NOT NULL AND addressLongitude IS NOT NULL';

        return $sql;
    }

    /**
     * Perform a basic search if no results are returned from primary search
     *
     * @param string $key
     *
     * @return string
     */
    public static function get_backup_search_sql($key)
    {
        $API = new PerchAPI(1.0, 'root_locator');
        $db = $API->get('DB');

        $sql = '
            SELECT \'' . __CLASS__ . '\' AS source, addressTitle AS score, addressTitle, addressBuilding, addressStreet, addressPostcode, addressID, "", "", ""
            FROM ' . PERCH_DB_PREFIX . 'root_locator_addresses
            WHERE addressLatitude IS NOT NULL AND addressLongitude IS NOT NULL
            AND (
                concat("  ", addressTitle, "  ") REGEXP ' . $db->pdb('[[:<:]]' . $key . '[[:>:]]') . '
                OR  concat("  ", addressBuilding, "  ") REGEXP ' . $db->pdb('[[:<:]]' . $key . '[[:>:]]') . '
                OR  concat("  ", addressStreet, "  ") REGEXP ' . $db->pdb('[[:<:]]' . $key . '[[:>:]]') . '
                OR  concat("  ", addressPostcode, "  ") REGEXP ' . $db->pdb('[[:<:]]' . $key . '[[:>:]]') .
            ')';

        return $sql;
    }

    /**
     * Format results for display in search results template
     *
     * @param $key
     * @param $opts
     * @param $result
     *
     * @return array
     */
    public static function format_result($key, $opts, $result)
    {
        $result['addressTitle'] = $result['col1'];
        $result['addressBuilding'] = $result['col2'];
        $result['addressStreet'] = $result['col3'];
        $result['addressPostcode'] = $result['col4'];
        $result['addressID'] = $result['col5'];
        $result['_id'] = $result['col5'];

        $html = implode(', ', [
            $result['addressTitle'],
            $result['addressBuilding'],
            $result['addressStreet'],
            $result['addressPostcode'],
            $result['addressPostcode']
        ]);

        $html = preg_replace('/(' . $key . ')/i', '<em class="keyword">$1</em>', $html);

        $match = [];
        $match['title'] = $result['addressTitle'];
        $match['excerpt'] = $html;
        $match['key'] = $key;
        $match['url'] = false;

        return $match;
    }

    /**
     * Format results for display in Perch admin sidebar
     *
     * @param $key
     * @param $options
     * @param $result
     *
     * @return mixed
     */
    public static function format_admin_result($key, $options, $result)
    {
        $result['addressID'] = $result['col5'];

        $self = __CLASS__;

        $out = $self::format_result($key, $options, $result);

        $out['url'] = PERCH_LOGINPATH . '/addons/apps/root_locator/edit/?id=' . $result['addressID'];

        return $out;
    }
}
