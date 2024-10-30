<?php

/**
 * WWE Small Get Distance
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Distance Request Class
 */
class Get_ups_freight_distance
{

    function __construct()
    {
        add_filter("en_wd_get_address", array($this, "sm_address"), 10, 2);
    }

    /**
     * Get Address Upon Access Level
     * @param $map_address
     * @param $accessLevel
     */
    function ups_freight_address($map_address, $accessLevel, $destinationZip = array())
    {

        $domain = ups_ltl_get_domain();
        $postData = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('ups_freight_setting_licnse_key'),
            'ServerName' => $_SERVER['SERVER_NAME'],
            'ServerName' => $domain,
        );
        $Ups_Small_Curl_Request = new UPS_Curl_Request();
        $output = $Ups_Small_Curl_Request->ups_get_curl_response(UPS_FREIGHT_DOMAIN_HITTING_URL . '/addon/google-location.php', $postData);
        return $output;
    }

}
