<?php

/**
 * UPS Freight Quote Settings
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class For Quote Settings Tab
 */
class UPS_Freight_Quote_Settings {

    /**
     * Quote Setting Fields
     */
    function ups_freight_quote_settings_tab() {
        // Cuttoff Time
        $ups_disable_cutt_off_time_ship_date_offset = "";
        $ups_cutt_off_time_package_required = "";

        //  Check the cutt of time & offset days plans for disable input fields
        $ups_action_cutOffTime_shipDateOffset = apply_filters('ups_freight_quotes_quotes_plans_suscription_and_features', 'ups_cutt_off_time');
        if (is_array($ups_action_cutOffTime_shipDateOffset)) {
            $ups_disable_cutt_off_time_ship_date_offset = "disabled_me";
            $ups_cutt_off_time_package_required = apply_filters('ups_freight_quotes_plans_notification_link', $ups_action_cutOffTime_shipDateOffset);
        }

        $ltl_enable = get_option('en_plugins_return_LTL_quotes');
        $weight_threshold_class = $ltl_enable == 'yes' ? 'show_en_weight_threshold_lfq' : 'hide_en_weight_threshold_lfq';
        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

        echo '<div class="ups_freight_quote_section">';
        $settings = array(
            'section_title_quote' => array(
                'title' => __('', 'ups_freight_wc_settings'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'ups_freight_section_title'
            ),
            'ups_freight_label_as' => array(
                'name' => __('Label As ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'What the user sees during checkout, e.g. "LTL Freight". If left blank, "Freight" will display as the shipping method.',
                'id' => 'ups_freight_setting_label_as'
            ),
            'price_sort_ups_freight' => array(
                'name' => __("Don't sort shipping methods by price", 'woocommerce-settings-ups_freight_quotes'),
                'type' => 'checkbox',
                'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order. Enable this setting if you don’t want the plugin to perform this operation.',
                'id' => 'shipping_methods_do_not_sort_by_price'
            ),
            //** Start Delivery Estimate Options - Cuttoff Time
            'service_ups_estimates_title' => array(
                'name' => __('Delivery Estimate Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'desc' => '',
                'id' => 'service_ups_estimates_title'
            ),
            'ups_show_delivery_estimates_options_radio' => array(
                'name' => __("", 'woocommerce-settings-ups'),
                'type' => 'radio',
                'default' => 'dont_show_estimates',
                'options' => array(
                    'dont_show_estimates' => __("Don't display delivery estimates.", 'woocommerce'),
                    'delivery_days' => __("Display estimated number of days until delivery.", 'woocommerce'),
                    'delivery_date' => __("Display estimated delivery date.", 'woocommerce'),
                ),
                'id' => 'ups_delivery_estimates',
                'class' => 'ups_dont_show_estimate_option',
            ),
            //** End Delivery Estimate Options
            //**Start: Cut Off Time & Ship Date Offset
            'cutOffTime_shipDateOffset_ups_freight' => array(
                'name' => __('Cut Off Time & Ship Date Offset ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => $ups_cutt_off_time_package_required,
                'id' => 'ups_freight_cutt_off_time_ship_date_offset'
            ),
            'orderCutoffTime_ups_freight' => array(
                'name' => __('Order Cut Off Time ', 'woocommerce-settings-ups_freight_freight_orderCutoffTime'),
                'type' => 'text',
                'placeholder' => '-- : -- --',
                'desc' => 'Enter the cut off time (e.g. 2.00) for the orders. Orders placed after this time will be quoted as shipping the next business day.',
                'id' => 'ups_freight_order_cut_off_time',
                'class' => $ups_disable_cutt_off_time_ship_date_offset,
            ),
            'shipmentOffsetDays_ups_freight' => array(
                'name' => __('Fullfillment Offset Days ', 'woocommerce-settings-ups_freight_shipment_offset_days'),
                'type' => 'text',
                'desc' => 'The number of days the ship date needs to be moved to allow the processing of the order.',
                'placeholder' => 'Fullfillment Offset Days, e.g. 2',
                'id' => 'ups_freight_shipment_offset_days',
                'class' => $ups_disable_cutt_off_time_ship_date_offset,
            ),
            'all_shipment_days_ups' => array(
                'name' => __("What days do you ship orders?", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Select All',
                'class' => "all_shipment_days_ups $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'all_shipment_days_ups'
            ),
            'monday_shipment_day_ups' => array(
                'name' => __("", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Monday',
                'class' => "ups_shipment_day $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'monday_shipment_day_ups'
            ),
            'tuesday_shipment_day_ups' => array(
                'name' => __("", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Tuesday',
                'class' => "ups_shipment_day $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'tuesday_shipment_day_ups'
            ),
            'wednesday_shipment_day_ups' => array(
                'name' => __("", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Wednesday',
                'class' => "ups_shipment_day $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'wednesday_shipment_day_ups'
            ),
            'thursday_shipment_day_ups' => array(
                'name' => __("", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Thursday',
                'class' => "ups_shipment_day $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'thursday_shipment_day_ups'
            ),
            'friday_shipment_day_ups' => array(
                'name' => __("", 'woocommerce-settings-ups_quotes'),
                'type' => 'checkbox',
                'desc' => 'Friday',
                'class' => "ups_shipment_day $ups_disable_cutt_off_time_ship_date_offset",
                'id' => 'friday_shipment_day_ups'
            ),
            'ups_freight_show_delivery_estimates' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce-settings-ups-freight-quotes'),
                'desc' => '',
                'id' => 'ups_freight_show_delivery_estimates',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),
            //**End: Cut Off Time & Ship Date Offset
            'accessorial_quoted_ups_freight' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'ups_freight_wc_settings'),
                'desc' => '',
                'id' => 'woocommerce_accessorial_quoted_ups_freight',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),
            'residential_delivery_options_label' => array(
                'name' => __('Residential Delivery', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'residential_delivery_options_label'
            ),
            'ups_freight_residential_delivery' => array(
                'name' => __('', 'ups_freight_wc_settings'),
                'type' => 'checkbox',
                'desc' => __('Always quote as residential delivery', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_setting_residential',
                'class' => 'accessorial_service UpsFreightCheckbox',
            ),
//          Auto-detect residential addresses notification
            'avaibility_auto_residential' => array(
                'name' => __('Auto-detect residential addresses', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_auto_residential'
            ),
            'liftgate_delivery_options_label' => array(
                'name' => __('Lift Gate Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'liftgate_delivery_options_label'
            ),
            'ups_freight_liftgate_delivery' => array(
                'name' => __('', 'ups_freight_wc_settings'),
                'type' => 'checkbox',
                'desc' => __('Always quote lift gate delivery', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_settings_liftgate',
                'class' => 'accessorial_service UpsFreightCheckbox checkbox_fr_add',
            ),
            'ups_freight_quotes_liftgate_delivery_as_option' => array(
                'name' => __('', 'ups_freight_wc_settings'),
                'type' => 'checkbox',
                'desc' => __('Offer lift gate delivery as an option', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_quotes_liftgate_delivery_as_option',
                'class' => 'accessorial_service checkbox_fr_add',
            ),
            // Use my liftgate notification
            'avaibility_lift_gate' => array(
                'name' => __('Always include lift gate delivery when a residential address is detected', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'class' => 'hidden',
                'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                'id' => 'avaibility_lift_gate'
            ),
            // Handling Unit
            'ups_label_handling_unit' => array(
                'name' => __('Handling Unit ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'class' => 'hidden',
                'id' => 'ups_label_handling_unit'
            ),
            'ups_freight_handling_weight' => array(
                'name' => __('Weight of Handling Unit  ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Enter in pounds the weight of your pallet, skid, crate or other type of handling unit you use. The amount entered will be added to shipment weight prior to requesting a quote.',
                'id' => 'ups_freight_settings_handling_weight'
            ),
            'ups_freight_maximum_handling_weight' => array(
                'name' => __('Maximum Weight per Handling Unit  ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Enter in pounds the maximum weight that can be placed on the handling unit.',
                'id' => 'ups_freight_maximum_handling_weight'
            ),
            'ups_freight_handling_fee' => array(
                'name' => __('Handling Fee / Markup ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Increases the amount of the returned quote by a specified amount prior to displaying it in the shopping cart. The number entered will be interpreted as dollars and cents unless it is followed by a % sign. For example, entering 5.00 will cause $5.00 to be added to the quotes. Entering 5.00% will cause each quote to be multiplied by 1.05 (= 1 + 5%).',
                'id' => 'ups_freight_settings_handling_fee'
            ),
            // Enale Logs
            'ups_freight_enale_logs' => array(
                'name' => __("Enable Logs  ", 'woocommerce_odfl_quote'),
                'type' => 'checkbox',
                'desc' => 'When checked, the Logs page will contain up to 25 of the most recent transactions.',
                'id' => 'ups_freight_enale_logs'
            ),
            'ups_freight_allow_other_plugins' => array(
                'name' => __('Show WooCommerce Shipping Options ', 'ups_freight_wc_settings'),
                'type' => 'select',
                'default' => '3',
                'desc' => __('Permit or prevent the display of shipping rates from other plugins.', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_settings_allow_other_plugins',
                'options' => array(
                    'yes' => __('YES', 'YES'),
                    'no' => __('NO', 'NO')
                )
            ),
            'return_ups_freight_quotes' => array(
                'name' => __('Return LTL quotes when an order parcel shipment weight exceeds the weight threshold ', 'ups_freight_wc_settings'),
                'type' => 'checkbox',
                'class' => 'UpsFreightCheckbox',
                'desc' => '<span class="description" >When checked, the LTL Freight Quote will return quotes when an order’s total weight exceeds the weight threshold (the maximum permitted by WWE and UPS), even if none of the products have settings to indicate that it will ship LTL Freight. To increase the accuracy of the returned quote(s), all products should have accurate weights and dimensions. </span>',
                'id' => 'en_plugins_return_LTL_quotes'
            ),
            // Cart weight threshold
            'en_weight_threshold_lfq' => array(
                'name' => __('Weight threshold for LTL Freight Quotes ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'default' => $weight_threshold,
                'class' => $weight_threshold_class,
                'desc' => __("", 'woocommerce-settings-xpo_quotes'),
                'id' => 'en_weight_threshold_lfq',
            ),
            'en_suppress_parcel_rates' => array(
                'name' => __("", 'ups_freight_wc_settings'),
                'type' => 'radio',
                'default' => 'display_parcel_rates',
                'options' => array(
                    'display_parcel_rates' => __("Continue to display parcel rates when the weight threshold is met.", 'ups_freight_wc_settings'),
                    'suppress_parcel_rates' => __("Suppress parcel rates when the weight threshold is met.", 'ups_freight_wc_settings'),
                ),
                'class' => 'en_suppress_parcel_rates',
                'id' => 'en_suppress_parcel_rates',
            ),
            //**Start: Shipper and ThirdParty fields
            'ups_freight_relation_to_shipper' => array(
                'name' => __('Relationship To Shipper', 'ups_freight_wc_settings'),
                'type' => 'select',
                'desc' => 'Select how you identify yourself when getting quotes on tforcefreight.com.',
                'id' => 'ups_freight_relation_to_shipper',
                'options' => array(
                    'shipper' => __('Shipper', 'Shipper'),
                    'thirdparty' => __('Third Party', 'Third Party'),
                )
            ),
            'ups_freight_thirdparty_country_or_territory' => array(
                'name' => __('Third Party Country or Territory', 'ups_freight_wc_settings'),
                'type' => 'select',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_thirdparty_country_or_territory',
                'options' => array(
                    'US' => __('United States', 'United States'),
                    'CA' => __('Canada', 'Canada'),
                    'GU' => __('Guam', 'Guam'),
                    'MX' => __('Mexico', 'Mexico'),
                    'PR' => __('Puerto Rico', 'Puerto Rico'),
                    'VI' => __('US Virgin Islands', 'US Virgin Islands'),
                )
            ),
            'ups_freight_thirdparty_postal_code' => array(
                'name' => __('Third Party Postal Code', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => 'Enter the third party postal code. (For US, enter only the 5 digit ZIP code.)',
                'id' => 'ups_freight_thirdparty_postal_code'
            ),
            'ups_freight_payerCity' => array(
                'name' => __('Payer City', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => '',
                'id' => 'ups_freight_payerCity'
            ),
            'ups_freight_payerState' => array(
                'name' => __('Payer State', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => '',
                'id' => 'ups_freight_payerState'
            ),
            //**Stop: Shipper and ThirdParty fields
            'unable_retrieve_shipping_clear_ups_freight' => array(
                'title' => __('', 'woocommerce'),
                'name' => __('', 'woocommerce-settings-ups-freight-quotes'),
                'desc' => '',
                'id' => 'unable_retrieve_shipping_clear_ups_freight',
                'css' => '',
                'default' => '',
                'type' => 'title',
            ),
            'unable_retrieve_shipping_ups_freight' => array(
                'name' => __('Checkout options if the plugin fails to return a rate ', 'woocommerce-settings-ups_freight_quetes'),
                'type' => 'title',
                'desc' => 'When the plugin is unable to retrieve shipping quotes and no other shipping options are provided by an alternative source:',
                'id' => 'wc_settings_unable_retrieve_shipping_ups_freight',
            ),
            'pervent_checkout_proceed_ups_freight' => array(
                'name' => __('', 'woocommerce-settings-ups_freight_quetes'),
                'type' => 'radio',
                'id' => 'pervent_checkout_proceed_ups_freight_packages',
                'options' => array(
                    'allow' => __('', 'woocommerce'),
                    'prevent' => __('', 'woocommerce'),
                ),
                'id' => 'wc_pervent_proceed_checkout_eniture',
            ),
            'section_end_quote' => array(
                'type' => 'sectionend',
                'id' => 'ups_freight_settings_quote_section_end'
            )
        );
        return $settings;
    }

}
