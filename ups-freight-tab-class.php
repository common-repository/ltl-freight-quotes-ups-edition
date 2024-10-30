<?php
/**
 * UPS Freight Class WC_Settings_UPS_Freight
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Woocommerce Setting Tab Class
 */
class WC_Settings_UPS_Freight extends WC_Settings_Page
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'ups_freight_quotes';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * UPS FREIGHT Setting Tab For WooCommerce
     * @param array $settings_tabs
     */
    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs[$this->id] = __('TForce Freight', 'ups_freight_wc_settings');
        return $settings_tabs;
    }

    /**
     * UPS FREIGHT Setting Sections
     */

    public function get_sections()
    {
        $sections = array(
            '' => __('Connection Settings', 'ups_freight_wc_settings'),
            'section-1' => __('Quote Settings', 'ups_freight_wc_settings'),
            'section-2' => __('Warehouses', 'ups_freight_wc_settings'),
            // fdo va
            'section-4' => __('FreightDesk Online', 'ups_freight_wc_settings'),
            'section-5' => __('Validate Addresses', 'ups_freight_wc_settings'),
            'section-3' => __('User Guide', 'ups_freight_wc_settings')
        );

        // Logs data
        $enable_logs = get_option('ups_freight_enale_logs');
        if ($enable_logs == 'yes') {
            $sections['en-logs'] = 'Logs';
        }

        $sections = apply_filters('en_woo_addons_sections', $sections, en_woo_plugin_ups_freight_quotes);
        // Standard Packaging
        $sections = apply_filters('en_woo_pallet_addons_sections', $sections, en_woo_plugin_ups_freight_quotes);
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * UPS FREIGHT Warehouse Tab
     */
    public function ups_freight_warehouse()
    {
        require_once 'warehouse-dropship/wild/warehouse/warehouse_template.php';
        require_once 'warehouse-dropship/wild/dropship/dropship_template.php';
    }

    /**
     * FedEx Small User Guide Tab
     */

    public function ups_freight_user_guide()
    {
        include_once('template/guide.php');
    }

    /**
     * UPS Freight Settings
     * @param $section
     */
    public function ups_freight_settings($section = null)
    {
        ob_start();
        $conn_sec_class = new UPS_Freight_Connection_Settings;
        $quote_sec_class = new UPS_Freight_Quote_Settings;
        switch ($section) {
            case 'section-0':
                $settings = $conn_sec_class->ups_freight_connection_settings_tab();
                break;
            case 'section-1':
                $settings = $quote_sec_class->ups_freight_quote_settings_tab();
                break;
            case 'section-2' :
                $this->ups_freight_warehouse();
                $settings = array();
                break;
            case 'section-3' :
                $this->ups_freight_user_guide();
                $settings = array();
                break;
            // fdo va
            case 'section-4' :
                $this->freightdesk_online_section();
                $settings = [];
                break;

            case 'section-5' :
                $this->validate_addresses_section();
                $settings = [];
                break;

            case 'en-logs' :
                $this->shipping_logs_section();
                $settings = [];
                break;


            default:
                $settings = $conn_sec_class->ups_freight_connection_settings_tab();
                break;
        }

        $settings = apply_filters('en_woo_addons_settings', $settings, $section, en_woo_plugin_ups_freight_quotes);
        // Standard Packaging
        $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, en_woo_plugin_ups_freight_quotes);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('ups_freight_wc_settings', $settings, $section);
    }

    /**
     * avaibility_addon
     * @param array type $settings
     * @return array type
     */
    function avaibility_addon($settings)
    {
        if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
            unset($settings['avaibility_lift_gate']);
            unset($settings['avaibility_auto_residential']);
        }

        return $settings;
    }

    /**
     * Output
     * @global $current_section
     */
    public function output()
    {
        global $current_section;
        $settings = $this->ups_freight_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * UPS FREIGHT Save Settings
     */
    public function save()
    {
        global $current_section;
        $settings = $this->ups_freight_settings($current_section);
        // Cuttoff Time
        if (isset($_POST['ups_freight_order_cut_off_time']) && $_POST['ups_freight_order_cut_off_time'] != '') {
            $time_24_format = $this->ups_get_time_in_24_hours($_POST['ups_freight_order_cut_off_time']);
            $_POST['ups_freight_order_cut_off_time'] = $time_24_format;
        }
        WC_Admin_Settings::save_fields($settings);
    }

    /**
     * Cuttoff Time
     * @param $timeStr
     * @return false|string
     */
    public function ups_get_time_in_24_hours($timeStr)
    {
        $cutOffTime = explode(' ', $timeStr);
        $hours = $cutOffTime[0];
        $separator = $cutOffTime[1];
        $minutes = $cutOffTime[2];
        $meridiem = $cutOffTime[3];
        $cutOffTime = "{$hours}{$separator}{$minutes} $meridiem";
        return date("H:i", strtotime($cutOffTime));
    }
    // fdo va
    /**
     * FreightDesk Online section
     */
    public function freightdesk_online_section()
    {
        include_once plugin_dir_path(__FILE__) . 'fdo/freightdesk-online-section.php';
    }

    /**
     * Validate Addresses Section
     */
    public function validate_addresses_section()
    {
        include_once plugin_dir_path(__FILE__) . 'fdo/validate-addresses-section.php';
    }

    /**
     * Shipping Logs Section
    */
    public function shipping_logs_section()
    {
        include_once plugin_dir_path(__FILE__) . 'logs/en-logs.php';
    }
}

return new WC_Settings_UPS_Freight();
