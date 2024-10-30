<?php

/**
 * UPS Freight Class UPS_Freight_Shipping_Get_Package
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Shipping Package Class
 */
class UPS_Freight_Shipping_Get_Package
{
    /** $hasLTLShipment */
    public $hasLTLShipment = 0;

    /** $errors */
    public $errors = [];
    public $ValidShipmentsUps = 0;
    public $ValidShipmentsArr = [];
    // Images for FDO
    public $en_fdo_image_urls = [];
    // Micro Warehouse
    public $products = [];
    public $dropship_location_array = [];
    public $warehouse_products = [];
    public $origin = [];
    public $destination_Address_ups;

    /**
     * Grouping For Shipments
     * @param $package
     * @param $ups_freight_res_inst
     * @param $freight_zipcode
     * @return boolean|int
     * @global $wpdb
     */
    function group_ups_freight_shipment($package, $ups_freight_res_inst, $freight_zipcode)
    {
        $ups_freight_package = [];
        if (empty($freight_zipcode)) {
            return [];
        }
        global $wpdb;
        $weight = 0;
        $dimensions = 0;
        $validShipmentForLtlUps = $ups_freight_enable = false;
        $ups_freight_woo_obj = new UPS_Freight_Woo_Update_Changes();
        $ups_freight_zipcode = $ups_freight_woo_obj->ups_freight_postcode();

        $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
        $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
        $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

        // Micro Warehouse
        $smallPluginExist = 0;
        $ups_freight_package = $items = $items_shipment = [];
        $ups_get_shipping_quotes = new UPS_Freight_Get_Shipping_Quotes();
        $this->destination_Address_ups = $ups_get_shipping_quotes->destinationAddressUps();

        // Standard Packaging
        $en_ppp_pallet_product = apply_filters('en_ppp_existence', false);

        $flat_rate_shipping_addon = apply_filters('en_add_flat_rate_shipping_addon', false);
        $counter_ups = 0;
        foreach ($package['contents'] as $item_id => $values) {
            $_product = $values['data'];

            // Images for FDO
            $this->en_fdo_image_urls($values, $_product);

            // Flat rate pricing
            $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            $parent_id = $product_id;
            if(isset($values['variation_id']) && $values['variation_id'] > 0){
                $variation = wc_get_product($values['variation_id']);
                $parent_id = $variation->get_parent_id();
            }
            $en_flat_rate_price = $this->en_get_flat_rate_price($values, $_product);
            if ($flat_rate_shipping_addon && isset($en_flat_rate_price) && strlen($en_flat_rate_price) > 0) {
                continue;
            }

            // Flat shipping
            $en_fs_existence = apply_filters('en_fs_existence', false);
            $fs = get_post_meta($product_id, '_enable_fs', true);
            if ($en_fs_existence && $fs == 'yes') {
                continue;
            }

            // Get product shipping class
            $en_ship_class = strtolower($values['data']->get_shipping_class());
            if (in_array($en_ship_class, $en_get_current_classes_arr)) {
                continue;
            }

            // Shippable handling units
            $values = apply_filters('en_shippable_handling_units_request', $values, $values, $_product);
            $shippable = [];
            if (isset($values['shippable']) && !empty($values['shippable'])) {
                $shippable = $values['shippable'];
            }

            // Standard Packaging
            $ppp_product_pallet = [];
            $values = apply_filters('en_ppp_request', $values, $values, $_product);
            if (isset($values['ppp']) && !empty($values['ppp'])) {
                $ppp_product_pallet = $values['ppp'];
            }

            $ship_as_own_pallet = $vertical_rotation_for_pallet = 'no';
            if (!$en_ppp_pallet_product) {
                $ppp_product_pallet = [];
            }

            extract($ppp_product_pallet);

            $nestedPercentage = 0;
            $nestedDimension = "";
            $nestedItems = "";
            $StakingProperty = "";

            $height = is_numeric($_product->get_height()) ? $_product->get_height() : 0;
            $width = is_numeric($_product->get_width()) ? $_product->get_width() : 0;
            $length = is_numeric($_product->get_length()) ? $_product->get_length() : 0;
            $height = wc_get_dimension($height, 'in');
            $width = wc_get_dimension($width, 'in');
            $length = wc_get_dimension($length, 'in');
            $product_weight = wc_get_weight($_product->get_weight(), 'lbs');
            $weight = $product_weight * $values['quantity'];
            $dimensions = (($length * $values['quantity']) * $width * $height);
            $freightClassUps = $_product->get_shipping_class();
            $locationId = 0;
            (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();

            $locations_list = $this->ups_get_locations_list($post_id, $_product, $values);
            $origin_address = $ups_freight_res_inst->ups_freight_multi_warehouse($locations_list, $ups_freight_zipcode);
            
            $freightClass_ltl_gross = $this->ups_get_freight_class($_product, $values['variation_id'], $values['product_id']);

            ($freightClass_ltl_gross == 'Null') ? $freightClass_ltl_gross = "" : "";

            $ups_freight_enable = $this->get_ups_enable($_product);

            $product_markup = $this->ups_get_product_level_markup($_product, $values['variation_id'], $values['product_id'], $values['quantity']);

            if (!empty($origin_address)) {
                $locationId = (isset($origin_address['id'])) ? $origin_address['id'] : $origin_address['locationId'];
                $ups_freight_package[$locationId]['origin'] = $origin_address;

                // Micro Warehouse
                (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
                $this->products[] = $post_id;

                // Nested Material
                $nested_material = $this->en_nested_material($values, $_product);
                if ($nested_material == "yes") {
                    $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                    $nestedPercentage = get_post_meta($post_id, '_nestedPercentage', true);
                    $nestedDimension = get_post_meta($post_id, '_nestedDimension', true);
                    $nestedItems = get_post_meta($post_id, '_maxNestedItems', true);
                    $StakingProperty = get_post_meta($post_id, '_nestedStakingProperty', true);
                }

                // Hazardous Material
                $hazardous_material = $this->en_hazardous_material($values, $_product);
                $hm_plan = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
                $hm_status = (!is_array($hm_plan) && $hazardous_material == 'yes') ? TRUE : FALSE;

                // Shippable handling units
                $lineItemPalletFlag = $lineItemPackageCode = $lineItemPackageType = '0';
                extract($shippable);

                $product_title = str_replace(array("'", '"'), '', $_product->get_title());
                if (!$_product->is_virtual()) {
                    $en_items = array(
                        'productId' => $parent_id,
                        'productName' => str_replace(array("'", '"'), '', $_product->get_name()),
                        'productQty' => $values['quantity'],
                        'productPrice' => $_product->get_price(),
                        'productWeight' => $product_weight,
                        'productLength' => $length,
                        'productWidth' => $width,
                        'productHeight' => $height,
                        'productClass' => $freightClass_ltl_gross,
                        'ptype' => $ups_freight_enable,
                        'freightClass' => $freightClassUps,
                        'product_name' => $values['quantity'] . " x " . $product_title,
                        'products' => $product_title,
                        'hazardousMaterial' => $hm_status,
                        'hazardous_material' => $hm_status,
                        'hazmat' => $hm_status,
                        'productType' => ($_product->get_type() == 'variation') ? 'variant' : 'simple',
                        'productSku' => $_product->get_sku(),
                        'actualProductPrice' => $_product->get_price(),
                        'attributes' => $_product->get_attributes(),
                        'variantId' => ($_product->get_type() == 'variation') ? $_product->get_id() : '',
                        // Nesting
                        'nestedMaterial' => $nested_material,
                        'nestedPercentage' => $nestedPercentage,
                        'nestedDimension' => $nestedDimension,
                        'nestedItems' => $nestedItems,
                        'stakingProperty' => $StakingProperty,

                        // Shippable handling units
                        'lineItemPalletFlag' => $lineItemPalletFlag,
                        'lineItemPackageCode' => $lineItemPackageCode,
                        'lineItemPackageType' => $lineItemPackageType,

                        // Standard Packaging
                        'ship_as_own_pallet' => $ship_as_own_pallet,
                        'vertical_rotation_for_pallet' => $vertical_rotation_for_pallet,

                        'markup' => $product_markup
                    );

                    // Hook for flexibility adding to package
                    $en_items = apply_filters('en_group_package', $en_items, $values, $_product);
                    // NMFC Number things
                    $en_items = $this->en_group_package($en_items, $values, $_product);
                    // Micro Warehouse
                    $items[$post_id] = $en_items;

                    $ups_freight_package[$locationId]['items'][$counter_ups] = $en_items;

                    //  Hazardous Material
                    if ($hazardous_material == "yes" && !isset($ups_freight_package[$locationId]['hazardous_material'])) {
                        $ups_freight_package[$locationId]['hazardous_material'] = TRUE;
                    }

                    $forceAllowShipMethodUps = $this->forceAllowShipMethodUps($ups_freight_package[$locationId]['items'][$counter_ups]);
                    (isset($forceAllowShipMethodUps) && ($forceAllowShipMethodUps === 1)) ? $validShipmentForLtlUps = 1 : "";
                    $ups_freight_package[$locationId]['items'][$counter_ups]['validForLtl'] = $forceAllowShipMethodUps;
                }
            }

            $exceedWeight = get_option('en_plugins_return_LTL_quotes');
            $ups_freight_package[$locationId]['shipment_weight'] = isset($ups_freight_package[$locationId]['shipment_weight']) ? $ups_freight_package[$locationId]['shipment_weight'] + $weight : $weight;

            $ups_freight_package[$locationId]['validShipmentForLtl'] = $validShipmentForLtlUps;
            (isset($validShipmentForLtlUps) && ($validShipmentForLtlUps === 1)) ? $this->ValidShipmentsUps = 1 : "";

            // Micro Warehouse
            $items_shipment[$post_id] = $ups_freight_enable;

            $smallPluginExist = 0;
            $calledMethod = [];
            $eniturePluigns = json_decode(get_option('EN_Plugins'));

            if (!empty($eniturePluigns)) {
                foreach ($eniturePluigns as $enIndex => $enPlugin) {
                    $freightSmallClassName = 'WC_' . $enPlugin;

                    if (!in_array($freightSmallClassName, $calledMethod)) {
                        if (class_exists($freightSmallClassName)) {
                            $smallPluginExist = 1;
                        }
                        $calledMethod[] = $freightSmallClassName;
                    }
                }
            }

            // Cart weight threshold
            $cart_weight_threshold = get_option('en_weight_threshold_lfq');
            $cart_weight_threshold = (isset($cart_weight_threshold) && !empty($cart_weight_threshold)) ? $cart_weight_threshold : 150;

            if ($ups_freight_enable == true || ($ups_freight_package[$locationId]['shipment_weight'] >= $cart_weight_threshold && $exceedWeight == 'yes')) {
                $ups_freight_package[$locationId]['upsfreight'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArr[] = "ltl_freight";
            } elseif (isset($ups_freight_package[$locationId]['upsfreight'])) {
                $ups_freight_package[$locationId]['upsfreight'] = 1;
                $this->hasLTLShipment = 1;
                $this->ValidShipmentsArr[] = "ltl_freight";
            } elseif ($smallPluginExist == 1) {
                $ups_freight_package[$locationId]['small'] = 1;
                $this->ValidShipmentsArr[] = "small_shipment";
            } else {
                $this->ValidShipmentsArr[] = "no_shipment";
            }

            if (empty($ups_freight_package[$locationId]['items'])) {
                unset($ups_freight_package[$locationId]);
                $ups_freight_package[$locationId]["NOPARAM"] = 1;
            }
            $counter_ups++;
        }

        // Micro Warehouse
        $eniureLicenceKey = get_option('ups_freight_setting_licnse_key');
        $ups_freight_package = apply_filters('en_micro_warehouse', $ups_freight_package, $this->products, $this->dropship_location_array, $this->destination_Address_ups, $this->origin, $smallPluginExist, $items, $items_shipment, $this->warehouse_products, $eniureLicenceKey, 'upsfreight');
        do_action("eniture_debug_mood", "Product Detail (UPS Freight)", $ups_freight_package);
        return $ups_freight_package;
    }

    /**
     * Set images urls | Images for FDO
     * @param array type $en_fdo_image_urls
     * @return array type
     */
    public function en_fdo_image_urls_merge($en_fdo_image_urls)
    {
        return array_merge($this->en_fdo_image_urls, $en_fdo_image_urls);
    }

    /**
     * Get images urls | Images for FDO
     * @param array type $values
     * @param array type $_product
     * @return array type
     */
    public function en_fdo_image_urls($values, $_product)
    {
        $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        $gallery_image_ids = $_product->get_gallery_image_ids();
        foreach ($gallery_image_ids as $key => $image_id) {
            $gallery_image_ids[$key] = $image_id > 0 ? wp_get_attachment_url($image_id) : '';
        }

        $image_id = $_product->get_image_id();
        $this->en_fdo_image_urls[$product_id] = [
            'product_id' => $product_id,
            'image_id' => $image_id > 0 ? wp_get_attachment_url($image_id) : '',
            'gallery_image_ids' => $gallery_image_ids
        ];

        add_filter('en_fdo_image_urls_merge', [$this, 'en_fdo_image_urls_merge'], 10, 1);
    }

    /**
     * Nested Material
     * @param array type $values
     * @param array type $_product
     * @return string type
     */
    function en_nested_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_nestedMaterials', true);
    }

    /**
     * Check for ltl freight
     * @param type $productData
     * @return int
     */
    function forceAllowShipMethodUps($productData)
    {

        if ((!isset($productData['freightClass']) || $productData['freightClass'] != "ltl_freight")) {
            return 0;
        }
        return 1;
    }

    /**
     * Check enable_dropship and get Locations list
     * @param $post_id
     * @return array
     * @global $wpdb
     */
    function ups_get_locations_list($post_id, $_product, $values)
    {
        global $wpdb;

        $locations_list = [];

        (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
        $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
        
        // ticket# 4070473536
        // Check dropship location in parent product also
        if (isset($values['variation_id']) && $values['variation_id'] > 0 && empty($enable_dropship)) {
            $post_id = isset($values['product_id']) ? $values['product_id'] : $post_id;
            $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
        }

        if ($enable_dropship == 'yes') {
            $get_loc = get_post_meta($post_id, '_dropship_location', true);
            if ($get_loc == '') {
                // Micro Warehouse
                $this->warehouse_products[] = $post_id;
                return array('error' => 'wwe small dp location not found!');
            }

            // Multi Dropship
            $multi_dropship = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'multi_dropship');

            if (is_array($multi_dropship)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship' LIMIT 1"
                );
            } else {
                $get_loc = ($get_loc !== '') ? maybe_unserialize($get_loc) : $get_loc;
                $get_loc = is_array($get_loc) ? implode(" ', '", $get_loc) : $get_loc;
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE id IN ('" . $get_loc . "')"
                );
            }

            // Micro Warehouse
            $this->multiple_dropship_of_prod($locations_list, $post_id);
            $eniture_debug_name = "Dropships";
        }

        if (empty($locations_list)) {
            // Multi Warehouse
            $multi_warehouse = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'multi_warehouse');
            if (is_array($multi_warehouse)) {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse' LIMIT 1"
                );
            } else {
                $locations_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
                );
            }

            // Micro Warehouse
            $this->warehouse_products[] = $post_id;
            $eniture_debug_name = "Warehouses";
        }

        do_action("eniture_debug_mood", "Quotes $eniture_debug_name (s)", $locations_list);
        return $locations_list;
    }

    // Micro Warehouse
    public function multiple_dropship_of_prod($locations_list, $post_id)
    {
        $post_id = (string)$post_id;

        foreach ($locations_list as $key => $value) {
            $dropship_data = $this->address_array($value);

            $this->origin["D" . $dropship_data['zip']] = $dropship_data;
            if (!isset($this->dropship_location_array["D" . $dropship_data['zip']]) || !in_array($post_id, $this->dropship_location_array["D" . $dropship_data['zip']])) {
                $this->dropship_location_array["D" . $dropship_data['zip']][] = $post_id;
            }
        }

    }

    // Micro Warehouse
    public function address_array($value)
    {
        $dropship_data = [];

        $dropship_data['locationId'] = (isset($value->id)) ? $value->id : "";
        $dropship_data['zip'] = (isset($value->zip)) ? $value->zip : "";
        $dropship_data['city'] = (isset($value->city)) ? $value->city : "";
        $dropship_data['state'] = (isset($value->state)) ? $value->state : "";
        // Origin terminal address
        $dropship_data['address'] = (isset($value->address)) ? $value->address : "";
        // Terminal phone number
        $dropship_data['phone_instore'] = (isset($value->phone_instore)) ? $value->phone_instore : "";
        $dropship_data['location'] = (isset($value->location)) ? $value->location : "";
        $dropship_data['country'] = (isset($value->country)) ? $value->country : "";
        $dropship_data['enable_store_pickup'] = (isset($value->enable_store_pickup)) ? $value->enable_store_pickup : "";
        $dropship_data['fee_local_delivery'] = (isset($value->fee_local_delivery)) ? $value->fee_local_delivery : "";
        $dropship_data['suppress_local_delivery'] = (isset($value->suppress_local_delivery)) ? $value->suppress_local_delivery : "";
        $dropship_data['miles_store_pickup'] = (isset($value->miles_store_pickup)) ? $value->miles_store_pickup : "";
        $dropship_data['match_postal_store_pickup'] = (isset($value->match_postal_store_pickup)) ? $value->match_postal_store_pickup : "";
        $dropship_data['checkout_desc_store_pickup'] = (isset($value->checkout_desc_store_pickup)) ? $value->checkout_desc_store_pickup : "";
        $dropship_data['enable_local_delivery'] = (isset($value->enable_local_delivery)) ? $value->enable_local_delivery : "";
        $dropship_data['miles_local_delivery'] = (isset($value->miles_local_delivery)) ? $value->miles_local_delivery : "";
        $dropship_data['match_postal_local_delivery'] = (isset($value->match_postal_local_delivery)) ? $value->match_postal_local_delivery : "";
        $dropship_data['checkout_desc_local_delivery'] = (isset($value->checkout_desc_local_delivery)) ? $value->checkout_desc_local_delivery : "";

        $dropship_data['sender_origin'] = $dropship_data['location'] . ": " . $dropship_data['city'] . ", " . $dropship_data['state'] . " " . $dropship_data['zip'];

        return $dropship_data;
    }

    function en_hazardous_material($values, $_product)
    {
        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
        return get_post_meta($post_id, '_hazardousmaterials', true);
    }

    /**
     * Get Freight Class
     * @param $_product
     * @param $variation_id
     * @param $product_id
     * @return string
     */
    function ups_get_freight_class($_product, $variation_id, $product_id)
    {
        if ($_product->get_type() == 'variation') {
            $variation_class = get_post_meta($variation_id, '_ltl_freight_variation', true);

            if(empty($variation_class) || $variation_class == 'get_parent') {
                $variation_class = get_post_meta($variation_id, '_ltl_freight', true);
            }

            if (empty($variation_class)) {
                $freightClass_ltl_gross = get_post_meta($product_id, '_ltl_freight', true);
            } else {
                if ($variation_class > 0) {
                    $freightClass_ltl_gross = $variation_class;
                } else {
                    $freightClass_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
                }
            }
        } else {
            $freightClass_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
        }

        if(is_array($freightClass_ltl_gross) && count($freightClass_ltl_gross) > 0){
            $freightClass_ltl_gross = reset($freightClass_ltl_gross);
        }

        return $freightClass_ltl_gross;
    }

    /**
     * Get UPS Freight Enable or not
     * @param $_product
     * @return string
     */
    function get_ups_enable($_product)
    {
        if ($_product->get_type() == 'variation') {
            $ship_class_id = $_product->get_shipping_class_id();
            if ($ship_class_id == 0) {
                $parent_data = $_product->get_parent_data();
                $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                $get_shipping_result = (isset($get_parent_term->slug)) ? $get_parent_term->slug : '';
            } else {
                $get_shipping_result = $_product->get_shipping_class();
            }

            $ups_freight_enable = ($get_shipping_result && $get_shipping_result == 'ltl_freight') ? true : false;
        } else {
            $get_shipping_result = $_product->get_shipping_class();
            $ups_freight_enable = ($get_shipping_result == 'ltl_freight') ? true : false;
        }
        return $ups_freight_enable;
    }

    /**
     * Grouping For Shipment Quotes
     * @param $quotes
     * @param $handlng_fee
     * @return int
     */
    function ups_freight_grouped_quotes($quotes, $handlng_fee)
    {
        $totalPrice = 0;
        $grandTotal = 0;
        $grandTotalWdoutLiftGate = 0;
        $liftgate_amnt = 0;
        $label_sfx_arr = "";

        foreach ($quotes as $multiValues) {
            if (isset($multiValues) && !empty($multiValues['cost'])) {
                $totalPrice = $multiValues['cost'];
                $grandTotal += $totalPrice && !empty($handlng_fee) ? $this->ups_freight_parse_handeling_fee($handlng_fee, $totalPrice) : $totalPrice;
                $totalPriceLiftGate = (isset($multiValues['surcharges']['LIFTGATE'])) ? $multiValues['surcharges']['LIFTGATE'] : 0;
                $grandTotalWdoutLiftGate += $totalPrice && !empty($handlng_fee) ? $this->ups_freight_parse_handeling_fee($handlng_fee, $totalPrice - $totalPriceLiftGate) : $totalPrice - $totalPriceLiftGate;
                (isset($multiValues['surcharges']['LIFTGATE']) && !empty($multiValues['surcharges']['LIFTGATE'])) ? $liftgate_amnt = $liftgate_amnt + $multiValues['surcharges']['LIFTGATE'] : '';
                (isset($multiValues['label_sfx_arr'])) ? $label_sfx_arr = $multiValues['label_sfx_arr'] : '';
            } else {
                $this->errors = 'no quotes return';
                continue;
            }
        }

        $freight = array(
            'total' => $grandTotal,
            'label_sfx_arr' => $label_sfx_arr,
            'liftgate_amnt' => $liftgate_amnt,
            'grandTotalWdoutLiftGate' => $grandTotalWdoutLiftGate,
        );

        return $freight;
    }

    /**
     * Grouping For Small Quotes
     * @param $smallQuotes
     * @return int
     */
    function ups_freight_get_small_package_cost($smallQuotes)
    {
        $result = [];
        $minCostArr = [];

        if (isset($smallQuotes) && count($smallQuotes) > 0) {
            foreach ($smallQuotes as $smQuotes) {
                $CostArr = [];
                if (!isset($smQuotes['error'])) {
                    foreach ($smQuotes as $smQuote) {
                        $CostArr[] = $smQuote['cost'];
                        $result['error'] = false;
                    }
                    $minCostArr[] = (count($CostArr) > 0) ? min($CostArr) : "";
                } else {
                    $result['error'] = !isset($result['error']) ? true : $result['error'];
                }
            }
            $result['price'] = (isset($minCostArr) && count($minCostArr) > 0) ? min($minCostArr) : "";
        } else {
            $result['error'] = false;
            $result['price'] = 0;
        }
        return $result;
    }

    /**
     * Calculate Handeling Fee
     * @param $handlng_fee
     * @param $cost
     * @return int
     */
    function ups_freight_parse_handeling_fee($handlng_fee, $cost)
    {
        $pos = strpos($handlng_fee, '%');
        if ($pos > 0) {
            $exp = explode(substr($handlng_fee, $pos), $handlng_fee);
            $get = $exp[0];
            $percnt = $get / 100 * $cost;
            $grandTotal = $cost + $percnt;
        } else {
            $grandTotal = $cost + $handlng_fee;
        }
        return $grandTotal;
    }

    /**
     * Get the product nmfc number
     */
    public function en_group_package($item, $product_object, $product_detail)
    {
        $en_nmfc_number = $this->en_nmfc_number($product_object, $product_detail);
        $item['nmfc_number'] = $en_nmfc_number;
        return $item;
    }

    /**
     * Get product shippable unit enabled
     */
    public function en_nmfc_number($product_object, $product_detail)
    {
        $post_id = (isset($product_object['variation_id']) && $product_object['variation_id'] > 0) ? $product_object['variation_id'] : $product_detail->get_id();
        $nmfc_number = get_post_meta($post_id, '_nmfc_number', true);
        if(is_array($nmfc_number) && count($nmfc_number) > 0){
            return reset($nmfc_number);
        }else{
            return $nmfc_number;
        }
    }

    /**
     * Returns product level markup
     */
    function ups_get_product_level_markup($_product, $variation_id, $product_id, $quantity)
    {
        $product_level_markup = 0;
        if ($_product->get_type() == 'variation') {
            $product_level_markup = get_post_meta($variation_id, '_en_product_markup_variation', true);
            if(empty($product_level_markup) || $product_level_markup == 'get_parent'){
                $product_level_markup = get_post_meta($_product->get_id(), '_en_product_markup', true);
            }
        } else {
            $product_level_markup = get_post_meta($_product->get_id(), '_en_product_markup', true);
        }

        if(empty($product_level_markup)) {
            $product_level_markup = get_post_meta($product_id, '_en_product_markup', true);
        }

        if(!empty($product_level_markup) && strpos($product_level_markup, '%') === false && is_numeric($product_level_markup) && is_numeric($quantity))
        {
            $product_level_markup *= $quantity;
        } else if(!empty($product_level_markup) && strpos($product_level_markup, '%') > 0 && is_numeric($quantity)){
            $position = strpos($product_level_markup, '%');
            $first_str = substr($product_level_markup, $position);
            $arr = explode($first_str, $product_level_markup);
            $percentage_value = $arr[0];
            $product_price = $_product->get_price();
 
            if (!empty($product_price)) {
                $product_level_markup = $percentage_value / 100 * ($product_price * $quantity);
            } else {
                $product_level_markup = 0;
            }
         }
 
        return $product_level_markup;
    }

    
    /**
     * Returns flat rate price and quantity
     */
    function en_get_flat_rate_price($values, $_product)
    {
        if ($_product->get_type() == 'variation') {
            $flat_rate_price = get_post_meta($values['variation_id'], 'en_flat_rate_price', true);
            if (strlen($flat_rate_price) < 1) {
                $flat_rate_price = get_post_meta($values['product_id'], 'en_flat_rate_price', true);
            }
        } else {
            $flat_rate_price = get_post_meta($_product->get_id(), 'en_flat_rate_price', true);
        }

        return $flat_rate_price;
    }

}
