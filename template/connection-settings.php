<?php
/**
 * Class UPS_Freight_Connection_Settings
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}


/**
 * UPS FREIGHT Connection Settings Tab Class
 */
class UPS_Freight_Connection_Settings
{
    /**
     * Connection Settings Fields
     * @return array
     */

    function ups_freight_connection_settings_tab()
    {
        $default_api = empty(get_option('ups_freight_setting_username')) ? 'ups_new_api' : 'ups_old_api';

        echo '<div class="ups_freight_tab_section">';
        $settings = array(
            'section_title_ups_freight' => array(
                'name' => __('', 'ups_freight_wc_settings'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'ups_freight_setting_connection_title',
            ),

            'ups_freight_api_endpoint' => array(
                'name' => __('Which API will you connect to? ', 'ups_freight_wc_settings'),
                'type' => 'select',
                'default' => $default_api,
                'id' => 'ups_freight_api_endpoint',
                'options' => array(
                    'ups_old_api' => __('Legacy API', 'Legacy API'),
                    'ups_new_api' => __('New API', 'New API')
                )
            ),

            // New API
            'ups_freight_client_id' => array(
                'name' => __('Client ID ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_client_id',
                'class' => 'ups_freight_new_api_field'
            ),

            'ups_freight_client_secret' => array(
                'name' => __('Client Secret ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_client_secret',
                'class' => 'ups_freight_new_api_field'
            ),
            'ups_freight_new_api_username' => array(
                'name' => __('Username ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_new_api_username',
                'class' => 'ups_freight_new_api_field'
            ),
            'ups_freight_new_api_password' => array(
                'name' => __('Password ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_new_api_password',
                'class' => 'ups_freight_new_api_field'
            ),

            // Old API
            'account_no_ups_freight' => array(
                'name' => __('Account Number ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_setting_account_no',
                'class' => 'ups_freight_old_api_field'
            ),
            
            'ups_freight_username' => array(
                'name' => __('Username ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_setting_username',
                'class' => 'ups_freight_old_api_field'
            ),

            'ups_freight_password' => array(
                'name' => __('Password ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_setting_password',
                'class' => 'ups_freight_old_api_field'
            ),

            'ups_freight_access_key' => array(
                'name' => __('API Access Key', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => '',
                'id' => 'ups_freight_setting_acccess_key',
                'class' => 'ups_freight_old_api_field'
            ),

            'ups_freight_licnse_key' => array(
                'name' => __('Eniture API Key ', 'ups_freight_wc_settings'),
                'type' => 'text',
                'desc' => __('Obtain a Eniture API Key Key from <a href="https://eniture.com/woocommerce-tforce-ltl-freight/" target="_blank" >eniture.com </a>', 'ups_freight_wc_settings'),
                'id' => 'ups_freight_setting_licnse_key'
            ),

            'ups_rates_based' => array(
                'name' => __('TForce rates my freight based on weight and...', 'woocommerce_ups_quote'),
                'type' => 'radio',
                'id' => 'ups_rates_based',
                'options' => array(
                    'freight' => __('Freight class', 'woocommerce'),
                    'dimension' => __('Dimensions', 'woocommerce'),
                ),
                'default' => 'freight',
            ),

            'section_end_ups_freight' => array(
                'type' => 'sectionend',
                'id' => 'ups_freight_access_level'
            ),
        );
        return $settings;
    }
}
