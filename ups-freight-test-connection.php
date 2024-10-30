<?php

/**
 * UPS Freight Test connection
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}


add_action('wp_ajax_nopriv_ups_freight_test_connection', 'ups_freight_test_submit');
add_action('wp_ajax_ups_freight_test_connection', 'ups_freight_test_submit');
/**
 * FedEx Small Test connection AJAX Request
 */
function ups_freight_test_submit()
{
    $data = array(
        'licence_key' => (isset($_POST['ups_freight_license'])) ? sanitize_text_field($_POST['ups_freight_license']) : "",
        'sever_name' => ups_ltl_get_domain(),
        'plateform' => 'WordPress',
        'carrierName' => 'ups',
        'carrier_mode' => 'test',
        'UserName' => (isset($_POST['ups_freight_username'])) ? sanitize_text_field($_POST['ups_freight_username']) : "",
        'Password' => (isset($_POST['ups_freight_password'])) ? sanitize_text_field($_POST['ups_freight_password']) : "",
    );

    if(isset($_POST['api_end_point']) && $_POST['api_end_point'] == 'ups_old_api'){
        $data['APIKey'] = (isset($_POST['ups_freight_key'])) ? sanitize_text_field($_POST['ups_freight_key']) : "";
        $data['AccountNumber'] = (isset($_POST['ups_freight_acc_no'])) ? sanitize_text_field($_POST['ups_freight_acc_no']) : "";
    }else{
        $data['requestForTForceQuotes'] = '1';
        $data['clientId'] = (isset($_POST['client_id'])) ? sanitize_text_field($_POST['client_id']) : "";
        $data['clientSecret'] = (isset($_POST['client_secret'])) ? sanitize_text_field($_POST['client_secret']) : "";
    }

    $ups_curl_obj = new UPS_Curl_Request();
    $sResponseData = $ups_curl_obj->ups_get_curl_response(UPS_FREIGHT_DOMAIN_HITTING_URL . '/index.php', $data);
    $sResponseData = json_decode($sResponseData);

    if (isset($sResponseData->severity) && $sResponseData->severity == 'SUCCESS' || isset($sResponseData->q->Rate)) {
        $sResult = array('message' => "success");
    }else if (isset($sResponseData->severity) && $sResponseData->severity == 'ERROR') {
        $message = (isset($sResponseData->Message) && !empty($sResponseData->Message)) ? $sResponseData->Message : "failure";
        $sResult = array('message' => $message);
    } elseif (isset($sResponseData->error) || isset($sResponseData->error->Description)) {
        $sResult = (isset($sResponseData->error->Description) && !empty($sResponseData->error->Description)) ? $sResponseData->error->Description : $sResponseData->error;
        $sResult = array('message' => $sResult);
    } else {
        $sResult = array('message' => "failure");
    }

    echo json_encode($sResult);
    exit();
}
