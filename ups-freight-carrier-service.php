<?php

/**
 * Class UPS_Freight_Get_Shipping_Quotes
 *
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get UPS FREIGHT Quotes Rate Class
 */
class UPS_Freight_Get_Shipping_Quotes extends Ups_Freight_Liftgate_As_Option
{

    /**
     * Create Shipping Package
     * @param $packages
     * @return array
     */
    public $hazardous_status;
    public $en_wd_origin_array;
    public $localdeliver;
    public $quote_settings;

    function ups_freight_shipping_array($packages, $package_plugin = "")
    {
        $destinationAddressUps = $this->destinationAddressUps();
        $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");

        $EnUpsfreightFdo = new EnUpsfreightFdo();
        $en_fdo_meta_data = array();

        // check plan for nested material
        $nested_plan = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'nested_material');
        $nestingPercentage = $nestedDimension = $nestedItems = $stakingProperty = [];
        $doNesting = false;
        $products = $product_name = $lineItem = array();
        $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : array();
        $product_markup_shipment_arr = [];
        foreach ($packages['items'] as $item) {

            // Standard Packaging
            $ship_as_own_pallet = isset($item['ship_as_own_pallet']) && $item['ship_as_own_pallet'] == 'yes' ? 1 : 0;
            $vertical_rotation_for_pallet = isset($item['vertical_rotation_for_pallet']) && $item['vertical_rotation_for_pallet'] == 'yes' ? 1 : 0;
            $ups_counter = (isset($item['variantId']) && $item['variantId'] > 0) ? $item['variantId'] : $item['productId'];
            $nmfc_num = (isset($item['nmfc_number'])) ? $item['nmfc_number'] : '';
            $lineItem[$ups_counter] = array(
                'typeOfHandlingUnit' => 'PLT',
                'typeOfHandlingUnitDescription' => 'PALLET',
                'lineItemHeight' => $item['productHeight'],
                'lineItemLength' => $item['productLength'],
                'lineItemWidth' => $item['productWidth'],
                'lineItemClass' => $item['productClass'],
                'lineItemWeight' => $item['productWeight'],
                'piecesOfLineItem' => $item['productQty'],
                'lineItemNMFC' => $nmfc_num,
                // Nesting
                'nestingPercentage' => $item['nestedPercentage'],
                'nestingDimension' => $item['nestedDimension'],
                'nestedLimit' => $item['nestedItems'],
                'nestedStackProperty' => $item['stakingProperty'],

                // Shippable handling units
                'lineItemPalletFlag' => $item['lineItemPalletFlag'],
                'lineItemPackageType' => $item['lineItemPackageType'],

                // Standard Packaging
                'shipPalletAlone' => $ship_as_own_pallet,
                'vertical_rotation' => $vertical_rotation_for_pallet
            );
            $lineItem[$ups_counter] = apply_filters('en_fdo_carrier_service', $lineItem[$ups_counter], $item);
            $product_name[] = $item['product_name'];
            $products[] = $item['products'];
            isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
            $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";
            if(!empty($item['markup'])){
                $product_markup_shipment_arr[$ups_counter] = $item['markup'];
            }
        }

        $getVersion = $this->upsLTLWcVersionNumber();
        $address = $packages['origin']['city'] . ", " . $packages['origin']['state'] . ", " . $packages['origin']['zip'] . ", " . $this->ups_get_country_code($packages['origin']['country']);
        $domain = ups_ltl_get_domain();
        $senderName = get_bloginfo();
        $senderName = (isset($senderName) && (strlen($senderName) > 0)) ? $senderName : "N";

        $wordpress_version = get_bloginfo('version');
        $wordpress_version = (isset($wordpress_version) && (strlen($wordpress_version) > 0)) ? $wordpress_version : "N";
        // Cuttoff Time
        $shipment_week_days = "";
        $order_cut_off_time = "";
        $shipment_off_set_days = "";
        $modify_shipment_date_time = "";
        $store_date_time = "";
        $ups_delivery_estimates = get_option('ups_delivery_estimates');
        $shipment_week_days = $this->ups_shipment_week_days();
        if ($ups_delivery_estimates == 'delivery_days' || $ups_delivery_estimates == 'delivery_date') {
            $order_cut_off_time = $this->quote_settings['orderCutoffTime'];
            $shipment_off_set_days = $this->quote_settings['shipmentOffsetDays'];
            $modify_shipment_date_time = ($order_cut_off_time != '' || $shipment_off_set_days != '' || (is_array($shipment_week_days) && count($shipment_week_days) > 0)) ? 1 : 0;
            $store_date_time = $today = date('Y-m-d H:i:s', current_time('timestamp'));
        }
        $en_fdo_meta_data = $EnUpsfreightFdo->en_cart_package($packages);
        $post_data = array(
            'plateform' => 'WordPress',
            'plugin_version' => $getVersion["upsLtl_plugin_version"],
            'wordpress_version' => $wordpress_version,
            'woocommerce_version' => $getVersion["woocommerce_plugin_version"],
            'licence_key' => get_option('ups_freight_setting_licnse_key'),
            'sever_name' => $this->ups_freight_parse_url($domain),
            'carrierName' => 'ups',
            'carrier_mode' => 'pro',
            'AccountNumber' => get_option('ups_freight_setting_account_no'),
            'suspend_residential' => get_option('suspend_automatic_detection_of_residential_addresses'),
            'residential_detecion_flag' => $residential_detecion_flag,
            //**Start: Shipper and ThirdParty fields
            'paymentType' => (get_option('ups_freight_relation_to_shipper') == 'thirdparty') ? 'ThirdParty' : get_option('ups_freight_relation_to_shipper'),
            'payerName' => 'eniture_ups_payer',
            'payerAddressLine' => 'eniture_ups_payer_address',
            'payerCity' => get_option('ups_freight_payerCity'),
            'payerState' => get_option('ups_freight_payerState'),
            'payerZip' => get_option('ups_freight_thirdparty_postal_code'),
            'payerCountryCode' => get_option('ups_freight_thirdparty_country_or_territory'),
            //**Stop: Shipper and ThirdParty fields
            'plateform' => 'WordPress',
            'senderCity' => $packages['origin']['city'],
            'senderState' => $packages['origin']['state'],
            'senderZip' => $packages['origin']['zip'],
            'senderCountryCode' => $this->ups_get_country_code($packages['origin']['country']),
            'senderName' => $senderName,
            'senderAddressLine' => $address,
            'receiverCity' => $destinationAddressUps['city'],
            'receiverState' => $destinationAddressUps['state'],
            'receiverZip' => $destinationAddressUps['zip'],
            'receiverCountryCode' => $this->ups_get_country_code($destinationAddressUps['country']),
            'liftgateDelivery' => (get_option('ups_freight_settings_liftgate') === 'yes') ? 'Y' : '',
            'residentialDelivery' => (get_option('ups_freight_setting_residential') === 'yes') ? 'Y' : '',
            'serviceCode' => '308',
            'serviceCodeDescription' => 'TForce Freight LTL',
            'timeInTransitIndicator' => 'Y',
            'commdityDetails' => array(
                'handlingUnitDetails' => array(
                    'wsHandlingUnit' => $lineItem,
                ),
            ),
            'handlingUnitWeight' => get_option('ups_freight_settings_handling_weight'),
            // Max Handling Unit
            'maxWeightPerHandlingUnit' => get_option('ups_freight_maximum_handling_weight'),
            'en_fdo_meta_data' => $en_fdo_meta_data,
            'sender_origin' => $packages['origin']['location'] . ": " . $packages['origin']['city'] . ", " . $packages['origin']['state'] . " " . $packages['origin']['zip'],
            'product_name' => $product_name,
            'products' => $products,
            'sender_location' => $packages['origin']['location'],
            'doNesting' => $doNesting,
            // Cuttoff Time
            'modifyShipmentDateTime' => $modify_shipment_date_time,
            'OrderCutoffTime' => $order_cut_off_time,
            'shipmentOffsetDays' => $shipment_off_set_days,
            'storeDateTime' => $store_date_time,
            'shipmentWeekDays' => $shipment_week_days,
            'origin_markup' => (isset($packages['origin']['origin_markup'])) ? $packages['origin']['origin_markup'] : 0,
            'product_level_markup_arr' => $product_markup_shipment_arr
        );

        if((empty(get_option('ups_freight_api_endpoint'))) || ('ups_old_api' == get_option('ups_freight_api_endpoint'))){
            $post_data['APIKey'] = get_option('ups_freight_setting_acccess_key');
            $post_data['UserName'] = get_option('ups_freight_setting_username');
            $post_data['Password'] = get_option('ups_freight_setting_password');
        }else{
            $post_data['requestForTForceQuotes'] = '1';
            $post_data['clientId'] = get_option('ups_freight_client_id');
            $post_data['clientSecret'] = get_option('ups_freight_client_secret');
            $post_data['UserName'] = get_option('ups_freight_new_api_username');
            $post_data['Password'] = get_option('ups_freight_new_api_password');
        }

        $post_data = $this->ups_freight_quotes_update_carrier_service($post_data);
        $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request", $post_data, en_woo_plugin_ups_freight_quotes);

//      Hazardous Material
        $hazardous_material = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'hazardous_material');

        if (!is_array($hazardous_material)) {
            $hazardous = array(
                'hazmatIndicator' => '1',
                'hazmatDetail' => array(
                    'name' => 'HAZ',
                    'phone' => array(
                        'number' => '73594821', // required :
                        'extension' => '21', // required :
                    ),
                    'transportationMode' => array(
                        'code' => 'Cargo Aircraft Only Passenger Aircraft', //required : can't be change. value from UPS
                        'description' => 'HAZ'  // Optional
                    )
                ),
            );

            (isset($packages['hazardous_material'])) ? $post_data = array_merge($post_data, $hazardous) : "";
            $post_data['en_fdo_meta_data'] = array_merge($post_data['en_fdo_meta_data'], $EnUpsfreightFdo->en_package_hazardous($packages, $en_fdo_meta_data));
            (isset($packages['hazardous_material']) == 'yes') ? $post_data['hazardous'][] = 'H' : '';
        }

//      In-store pickup and local delivery
        $instore_pickup_local_devlivery_action = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
        if (!is_array($instore_pickup_local_devlivery_action)) {
            $post_data = apply_filters('en_ups_freight_wd_standard_plans', $post_data, $post_data['receiverZip'], $this->en_wd_origin_array, $package_plugin);
        }

        $ups_rates_based = get_option('ups_rates_based');
        $ups_rates_based == 'dimension' ? $post_data['dimWeightBaseAccount'] = 1 : '';

        // Standard Packaging
        // Configure standard plugin with pallet packaging addon
        $post_data = apply_filters('en_pallet_identify', $post_data);

        do_action("eniture_debug_mood", "Quotes Request (ups ltl)", $post_data);
        do_action("eniture_debug_mood", "Build query (ups ltl)", http_build_query($post_data));
        do_action("eniture_debug_mood", "Plugin Features (ups ltl)", get_option('eniture_plugin_7'));
        
        return $post_data;
    }

    /**
     * @return shipment days of a week  - Cuttoff time
     */
    public function ups_shipment_week_days()
    {
        $shipment_days_of_week = array();

        if (get_option('all_shipment_days_ups') == 'yes') {
            return $shipment_days_of_week;
        }
        if (get_option('monday_shipment_day_ups') == 'yes') {
            $shipment_days_of_week[] = 1;
        }
        if (get_option('tuesday_shipment_day_ups') == 'yes') {
            $shipment_days_of_week[] = 2;
        }
        if (get_option('wednesday_shipment_day_ups') == 'yes') {
            $shipment_days_of_week[] = 3;
        }
        if (get_option('thursday_shipment_day_ups') == 'yes') {
            $shipment_days_of_week[] = 4;
        }
        if (get_option('friday_shipment_day_ups') == 'yes') {
            $shipment_days_of_week[] = 5;
        }

        return $shipment_days_of_week;
    }

    /**
     * Get UPS Country Code
     * @param $sCountryName
     */
    function ups_get_country_code($sCountryName)
    {
        switch (trim($sCountryName)) {
            case 'CN':
                $sCountryName = "CA";
                break;
            case 'CA':
                $sCountryName = "CA";
                break;
            case 'CAN':
                $sCountryName = "CA";
                break;
            case 'US':
                $sCountryName = "US";
                break;
            case 'USA':
                $sCountryName = "US";
                break;
        }
        return $sCountryName;
    }

    /**
     * destinationAddressUps
     * @return array type
     */
    function destinationAddressUps()
    {
        $en_order_accessories = apply_filters('en_order_accessories', []);
        if (isset($en_order_accessories) && !empty($en_order_accessories)) {
            return $en_order_accessories;
        }

        $ups_freight_woo_obj = new UPS_Freight_Woo_Update_Changes();
        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $ups_freight_woo_obj->ups_freight_postcode();
        $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $ups_freight_woo_obj->ups_freight_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $ups_freight_woo_obj->ups_freight_getCountry();
        $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $ups_freight_woo_obj->ups_freight_getCity();
        return array(
            'city' => $freight_city,
            'state' => $freight_state,
            'zip' => $freight_zipcode,
            'country' => $freight_country
        );
    }

    /**
     * Get Nearest Address If Multiple Warehouses
     * @param : array $warehous_list
     * @param : int $receiverZipCode
     * @return array
     */
    function ups_freight_multi_warehouse($warehous_list, $receiverZipCode)
    {
        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->ups_freight_origin_array($warehous_list);
        }

        $fedex_Small_distance_request = new Get_ups_freight_distance();
        $accessLevel = "MultiDistance";
        $response_json = $fedex_Small_distance_request->ups_freight_address($warehous_list, $accessLevel, $this->destinationAddressUps());

        $response_obj = json_decode($response_json);
        return $this->ups_freight_origin_array($response_obj->origin_with_min_dist);
    }

    /**
     * Create Origin Array
     * @param $origin
     * @return array Warehouse Address Array
     */
    function ups_freight_origin_array($origin)
    {
        // In-store pickup and local delivery
        if (has_filter("en_ups_freight_wd_origin_array_set")) {
            return apply_filters("en_ups_freight_wd_origin_array_set", $origin);
        }
        return array('locationId' => $origin->id, 'zip' => $origin->zip, 'city' => $origin->city, 'state' => $origin->state, 'location' => $origin->location, 'country' => $origin->country);
    }

    /**
     * Refine URL
     * @param : url $domain Domain URL
     */
    function ups_freight_parse_url($domain)
    {
        $domain = trim($domain);
        $parsed = parse_url($domain);

        if (empty($parsed['scheme'])) {
            $domain = 'http://' . ltrim($domain, '/');
        }

        $parse = parse_url($domain);
        $refinded_domain_name = $parse['host'];
        $domain_array = explode('.', $refinded_domain_name);

        if (in_array('www', $domain_array)) {
            $key = array_search('www', $domain_array);
            unset($domain_array[$key]);
            if(phpversion() < 8) {
                $refinded_domain_name = implode($domain_array, '.'); 
             }else {
                $refinded_domain_name = implode('.', $domain_array);
             }
        }
        return $refinded_domain_name;
    }

    function return_localdelivery_array()
    {
        return $this->localdeliver;
    }

    /**
     * Send Curl Request for getting Quotes
     * @param array $request_data Api Request Data Array
     */
    function ups_freight_get_web_quotes($request_data)
    {
        // get response from session
        $currentData = md5(json_encode($request_data));

        $requestFromSession = WC()->session->get('previousRequestData');

        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {
            do_action("eniture_debug_mood", "session Response (ltl)", json_decode($requestFromSession[$currentData]));
            $inst_data = $requestFromSession[$currentData];
            $inst_data = json_decode($inst_data);

            $this->localdeliver = isset($inst_data->InstorPickupLocalDelivery) && !empty($inst_data->InstorPickupLocalDelivery) ? $inst_data->InstorPickupLocalDelivery : array();

            return $this->parse_ups_freight_output($requestFromSession[$currentData], $request_data);
        }

        if (is_array($request_data) && count($request_data) > 0) {
            $ups_curl_obj = new UPS_Curl_Request();
            $output = $ups_curl_obj->ups_get_curl_response(UPS_FREIGHT_DOMAIN_HITTING_URL . '/index.php', $request_data);

//              set response in session
            $response = json_decode($output);
            $this->localdeliver = isset($response->InstorPickupLocalDelivery) && !empty($response->InstorPickupLocalDelivery) ? $response->InstorPickupLocalDelivery : array();

            do_action("eniture_debug_mood", "Response (ltl)", $response);
            if (isset($response->q) &&
                (empty($response->error)) &&
                (!empty($response->q->TotalShipmentCharge->MonetaryValue))) {
                if (isset($response->autoResidentialSubscriptionExpired) &&
                    ($response->autoResidentialSubscriptionExpired == 1)) {
                    $flag_api_response = "no";
                    $request_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($request_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }
            return $this->parse_ups_freight_output($output, $request_data);
        }
    }

    /**
     * Get Shipping Array For Single Shipment
     * @param array $output Quotes Array
     * @return array Single Quote Array
     */
    function parse_ups_freight_output($output, $request_data)
    {
        $result = json_decode($output);
        if(isset($result->severity) && $result->severity == 'ERROR'){
            return [];
        }

        //FDO
        $en_fdo_meta_data = (isset($request_data['en_fdo_meta_data'])) ? $request_data['en_fdo_meta_data'] : '';
        if (isset($result->debug)) {
            $en_fdo_meta_data['handling_unit_details'] = $result->debug;
        }

        $accessorials = [];
        ($this->quote_settings['liftgate_delivery'] == "yes") ? $accessorials[] = "L" : "";
        ($this->quote_settings['residential_delivery'] == "yes") ? $accessorials[] = "R" : "";
        (isset($request_data['hazardous']) && is_array($request_data['hazardous']) && in_array('H', $request_data['hazardous'])) ? $accessorials[] = "H" : "";
        $products = (isset($request_data['products'])) ? $request_data['products'] : [];

        // Standard Packaging
        $standard_packaging = (isset($result->standardPackagingData->response)) ? json_decode(json_encode($result->standardPackagingData->response), true) : [];

        if (isset($standard_packaging['pallets_packed']) && !empty($standard_packaging['pallets_packed'])) {
            foreach ($standard_packaging['pallets_packed'] as $bins_packed_key => $bins_packed_value) {
                $bin_items = (isset($bins_packed_value['items'])) ? $bins_packed_value['items'] : [];
                foreach ($bin_items as $bin_items_key => $bin_items_value) {
                    $bin_item_id = (isset($bin_items_value['id'])) ? $bin_items_value['id'] : '';
                    $get_product_name = (isset($products[$bin_item_id])) ? $products[$bin_item_id] : '';
                    if (isset($standard_packaging['pallets_packed'][$bins_packed_key]['items'][$bin_items_key])) {
                        $standard_packaging['pallets_packed'][$bins_packed_key]['items'][$bin_items_key]['product_name'] = $get_product_name;
                    }
                }
            }
        }

        $standard_packaging = (!isset($standard_packaging['response']) && !empty($standard_packaging)) ? ['response' => $standard_packaging] : $standard_packaging;

        $label_sufex_arr = $this->filter_label_sufex_array_ups_freight_quotes($result);
        $result = $this->format_response($result);

        // Cuttoff Time
        $delivery_estimates = (isset($result['totalTransitTimeInDays'])) ? $result['totalTransitTimeInDays'] : '';
        $delivery_time_stamp = (isset($result['deliveryDate'])) ? $result['deliveryDate'] : '';

        if (!empty($result) && !empty($result['cost'])) {

            $meta_data = [];
            $meta_data['sender_zip'] = (isset($request_data['senderZip'])) ? $request_data['senderZip'] : '';
            $meta_data['sender_location'] = (isset($request_data['sender_location'])) ? $request_data['sender_location'] : '';
            $meta_data['sender_origin'] = (isset($request_data['sender_origin'])) ? $request_data['sender_origin'] : '';
            $meta_data['product_name'] = (isset($request_data['product_name'])) ? json_encode($request_data['product_name']) : array();
            $meta_data['accessorials'] = json_encode($accessorials);
            $meta_data['standard_packaging'] = json_encode($standard_packaging);

            $quotes = array(
                'id' => 'ups_freight',
                'cost' => $result['cost'],
                'transit_time' => $result['transit_time'],
                'label_sfx_arr' => $label_sufex_arr,
                'meta_data' => $meta_data,
                'surcharges' => $result['surcharges'],
                // Cuttoff Time
                'delivery_estimates' => $delivery_estimates,
                'delivery_time_stamp' => $delivery_time_stamp,
                'origin_markup' => $request_data['origin_markup'],
                'product_level_markup_arr' => $request_data['product_level_markup_arr'],
                'plugin_name' => 'upsLtl',
                'plugin_type' => 'ltl',
                'owned_by' => 'eniture'
            );

            // FDO
            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            $en_fdo_meta_data['rate'] = $quotes;
            if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                unset($en_fdo_meta_data['rate']['meta_data']);
            }

            $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
            $quotes['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

            $quotes = apply_filters("en_woo_addons_web_quotes", $quotes, en_woo_plugin_ups_freight_quotes);

            $label_sufex = (isset($quotes['label_sufex'])) ? $quotes['label_sufex'] : array();
            $label_sufex = $this->label_R_wwe_ltl($label_sufex);
            $quotes['label_sufex'] = $label_sufex;

            in_array('R', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
            ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

            // Lift gate delivery as an option
            if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex)) ||
                    ($this->quote_settings['liftgate_resid_delivery'] != "yes"))) {
                $service = $quotes;
                $quotes['id'] .= "WL";

                (isset($quotes['label_sufex']) &&
                    (!empty($quotes['label_sufex']))) ?
                    array_push($quotes['label_sufex'], "L") : // IF
                    $quotes['label_sufex'] = array("L");       // ELSE

                // FDO
                $quotes['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                $quotes['append_label'] = " with lift gate delivery ";

                $liftgate_charge = (isset($service['surcharges']['LIFTGATE'])) ? $service['surcharges']['LIFTGATE'] : 0;
                $service['cost'] = (isset($service['cost'])) ? $service['cost'] - $liftgate_charge : 0;
                (!empty($service)) && (in_array("R", $service['label_sufex'])) ? $service['label_sufex'] = array("R") : $service['label_sufex'] = array();

                $simple_quotes = $service;

                // FDO
                if (isset($simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'])) {
                    $simple_quotes['meta_data']['en_fdo_meta_data']['rate']['cost'] = $service['cost'];
                }
            }

        } else {
            return [];
        }

        (!empty($simple_quotes)) ? $quotes['simple_quotes'] = $simple_quotes : "";

        return $quotes;
    }

    /**
     * check "R" in array
     * @param array type $label_sufex
     * @return array type
     */
    public function label_R_wwe_ltl($label_sufex)
    {
        if (get_option('ups_freight_residential') == 'yes' && (in_array("R", $label_sufex))) {
            $label_sufex = array_flip($label_sufex);
            unset($label_sufex['R']);
            $label_sufex = array_keys($label_sufex);
        }

        return $label_sufex;
    }

    /**
     * woocomerce and abf version
     * @return array
     */
    function upsLTLWcVersionNumber()
    {
        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $pluginFolder = get_plugins('/' . 'woocommerce');
        $pluginFile = 'woocommerce.php';
        $upsLtlPluginFolder = get_plugins('/' . 'ltl-freight-quotes-ups-edition');
        $upsLtlPluginFile = 'ltl-freight-quotes-ups-edition.php';
        $wcPlugin = (isset($pluginFolder[$pluginFile]['Version'])) ? $pluginFolder[$pluginFile]['Version'] : "";
        $upsLtlPlugin = (isset($upsLtlPluginFolder[$upsLtlPluginFile]['Version'])) ? $upsLtlPluginFolder[$upsLtlPluginFile]['Version'] : "";

        $pluginVersions = array(
            "woocommerce_plugin_version" => $wcPlugin,
            "upsLtl_plugin_version" => $upsLtlPlugin
        );

        return $pluginVersions;
    }

    /*
     * function returning methods added in shipping zone
     * return $enabled_methods
     */

    function get_enabled_shipping_methods()
    {

        $enabled_methods = array();
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        foreach ($shipping_methods as $id => $shipping_method) {
            if (isset($shipping_method->instance_settings['enabled']) && $shipping_method->instance_settings['enabled'] == 'yes'
            ) {
                $enabled_methods[] = $shipping_method->id;
            }
        }
        return $enabled_methods;
    }
    /**
     * Format the new and old API resposne
     */
    public function format_response($result){
        $response = [];
        if (isset($result->q)) {
            if(!empty($result->q->{308}->shipmentCharges->total->value)){

                $standard_ltl_rate_obj = $result->q->{308};

                $response['cost'] = $standard_ltl_rate_obj->shipmentCharges->total->value;
                $response['transit_time'] = (isset($standard_ltl_rate_obj->timeInTransit->timeInTransit)) ? $standard_ltl_rate_obj->timeInTransit->timeInTransit : '';
                $response['surcharges'] = (isset($standard_ltl_rate_obj->rate)) ? $this->parse_ups_freight_new_api_surchares($standard_ltl_rate_obj->rate) : [];
                $response['totalTransitTimeInDays'] = (isset($standard_ltl_rate_obj->totalTransitTimeInDays)) ? $standard_ltl_rate_obj->totalTransitTimeInDays : '';
                $response['deliveryDate'] = (isset($standard_ltl_rate_obj->deliveryDate)) ? $standard_ltl_rate_obj->deliveryDate : '';

            }else if(!empty($result->q->TotalShipmentCharge->MonetaryValue) && empty($result->error)){
                $response['cost'] = $result->q->TotalShipmentCharge->MonetaryValue;
                $response['transit_time'] = (isset($result->q->TimeInTransit->DaysInTransit)) ? $result->q->TimeInTransit->DaysInTransit : '';
                $response['surcharges'] = (isset($result->q->Rate)) ? $this->update_parse_ups_freight_quotes_output($result->q->Rate) : [];
                $response['totalTransitTimeInDays'] = (isset($result->q->totalTransitTimeInDays)) ? $result->q->totalTransitTimeInDays : '';
                $response['deliveryDate'] = (isset($result->q->deliveryDate)) ? $result->q->deliveryDate : '';
            }
        }

        return $response;
    }

}
