<?php
/*
  Plugin Name: LTL Freight Quotes - TForce Edition
  Plugin URI: https://eniture.com/products/
  Description: Dynamically retrieves your negotiated shipping rates from TForce Freight and displays the results in the WooCommerce shopping cart.
  Version: 3.6.3
  Author: Eniture Technology
  Author URI: https://eniture.com/
  Text Domain: eniture-technology
  License: GPL version 2 or later - http://www.eniture.com/
  WC requires at least: 6.4
  WC tested up to: 9.3.3
 */

/**
 * TForce Freight Plugin
 * @package     TForce Freight Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
define('UPS_FREIGHT_DOMAIN_HITTING_URL', 'https://ws029.eniture.com');
define('UPS_FREIGHT_FDO_HITTING_URL', 'https://freightdesk.online/api/updatedWoocomData');

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// Define reference
function en_ups_freight_plugin($plugins)
{
    $plugins['lfq'] = (isset($plugins['lfq'])) ? array_merge($plugins['lfq'], ['ups_freight' => 'UPS_Freight_Shipping_Class']) : ['ups_freight' => 'UPS_Freight_Shipping_Class'];
    return $plugins;
}

add_filter('en_plugins', 'en_ups_freight_plugin');

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($en = 1; $en <= 25; $en++) {
            $settings = get_option($eniture_plugins_id . $en);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

//Product detail set plans notification message for nested checkbox
if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

function valid_for_quotes_fun_ups($boolean)
{
    $boolean = TRUE;
    return $boolean;
}

add_filter("valid_for_quotes", "valid_for_quotes_fun_ups", 1);


if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Check WooCommerce installlation
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    add_action('admin_notices', 'ups_freight_wc_avaibility_err');
}

/**
 * UPS Freight Woo Availability Err
 */
function ups_freight_wc_avaibility_err()
{
    $class = "error";
    $message = "LTL Freight Quotes - TForce Edition is enabled, but not effective. It requires WooCommerce in order to work, Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin. Reactivate LTL Freight Quotes - TForce Edition plugin to create LTL shipping class.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

/**
 * Check WooCommerce version compatibility
 */
add_action('admin_init', 'ups_freight_check_woo_version');

/**
 * UPS Freight Check Woo Version
 */
if (!function_exists("ups_freight_check_woo_version")) {

    function ups_freight_check_woo_version()
    {
        $woo_version = ups_freight_wc_version_number();
        $version = '2.6';
        if (!version_compare($woo_version, $version, ">=")) {
            add_action('admin_notices', 'ups_freight_wc_version_failure');
        }
    }

}

/**
 * UPS Freight Woo Version Failure
 */
if (!function_exists("ups_freight_wc_version_failure")) {

    function ups_freight_wc_version_failure()
    {
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                _e('LTL Freight Quotes - TForce Edition plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure');
                ?>
            </p>
        </div>
        <?php
    }

}

/**
 * UPS Freight Woo Version Number
 * @return array WooCommerce version
 */
if (!function_exists("ups_freight_wc_version_number")) {

    function ups_freight_wc_version_number()
    {
        $plugin_folder = get_plugins('/' . 'woocommerce');
        $plugin_file = 'woocommerce.php';

        if (isset($plugin_folder[$plugin_file]['Version'])) {
            return $plugin_folder[$plugin_file]['Version'];
        } else {
            return NULL;
        }
    }

}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || is_plugin_active_for_network('woocommerce/woocommerce.php')) {
    /**
     * UPS Freight Admin Scripts
     */
    if (!function_exists("ups_freight_admin_script")) {

        function ups_freight_admin_script()
        {
            // Cuttoff Time
            wp_register_style('ups_wickedpicker_style', plugin_dir_url(__FILE__) . 'css/wickedpicker.min.css', false, '1.0.0');
            wp_register_script('ups_wickedpicker_script', plugin_dir_url(__FILE__) . 'js/wickedpicker.js', false, '1.0.0');
            wp_enqueue_style('ups_wickedpicker_style');
            wp_register_style('ups-freight-style', plugin_dir_url(__FILE__) . 'css/ups-freight-style.css', false, '1.1.2');
            wp_enqueue_style('ups-freight-style');
            wp_enqueue_style('ups_wickedpicker_style');
            wp_enqueue_script('ups_wickedpicker_script');
        }

        add_action('admin_enqueue_scripts', 'ups_freight_admin_script');
    }

    /**
     * Load scripts for Tforce Freight json tree view
     */
    if (!function_exists('en_tforce_jtv_script')) {
        function en_tforce_jtv_script()
        {
            wp_register_style('en_tforce_json_tree_view_style', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-style.css');
            wp_register_script('en_tforce_json_tree_view_script', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-script.js', ['jquery'], '1.0.0');

            wp_enqueue_style('en_tforce_json_tree_view_style');
            wp_enqueue_script('en_tforce_json_tree_view_script', [
                'en_tree_view_url' => plugins_url(),
            ]);
        }

        add_action('admin_init', 'en_tforce_jtv_script');
    }

    add_action('admin_enqueue_scripts', 'en_ups_freight_script');

    /**
     * Load Front-end scripts for ups
     */
    function en_ups_freight_script()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('en_ups_freight_script', plugin_dir_url(__FILE__) . 'js/en-ups-freight.js', array(), '1.0.8');
        wp_localize_script('en_ups_freight_script', 'en_ups_freight_admin_script', array(
            'plugins_url' => plugins_url(),
            'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
            'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
            // Cuttoff Time
            'ups_freight_order_cutoff_time' => get_option("ups_freight_order_cut_off_time"),
        ));
    }

    /**
     * Include Plugin Files
     */
    require_once('fdo/en-fdo.php');
    require_once('ups-freight-liftgate-as-option.php');
    require_once('template/connection-settings.php');
    require_once('template/quote-settings.php');
    require_once 'template/csv-export.php';

    require_once('warehouse-dropship/wild-delivery.php');
    require_once('warehouse-dropship/get-distance-request.php');
    require_once('standard-package-addon/standard-package-addon.php');

    require_once 'update-plan.php';

    require_once('ups-freight-test-connection.php');
    require_once('ups-freight-shipping-class.php');
    require_once('db/ups-freight-db.php');
    require_once('ups-freight-admin-filter.php');
    require_once('ups-freight-group-package.php');
    require_once('ups-freight-carrier-service.php');
    require_once('ups-freight-wc-update-change.php');
    require_once('ups-curl-class.php');
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    require_once('template/products-nested-options.php');

    // Origin terminal address
    add_action('admin_init', 'ups_freight_update_warehouse');

    require_once('product/en-product-detail.php');
    require_once 'order/en-order-export.php';
    require_once 'order/rates/order-rates.php';
    if (!has_filter('en_request_handler')) {
        require_once 'order/en-order-widget.php';
    }

    /**
     * UPS FREIGHT Activation Hook
     */
    register_activation_hook(__FILE__, 'create_ups_freight_class');
    register_activation_hook(__FILE__, 'create_ups_freight_wh_db');
    register_activation_hook(__FILE__, 'create_ups_freight_option');
    register_activation_hook(__FILE__, 'old_store_ups_ltl_dropship_status');

    register_activation_hook(__FILE__, 'en_ups_freight_activate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'en_ups_freight_deactivate_hit_to_update_plan');
    register_deactivation_hook(__FILE__, 'en_ups_freight_deactivate_plugin');

    /**
     * Hook to call when plugin update
     */
    function en_tforce_plugin_update( $upgrader_object, $options ) {
        $en_tforce_path_name = plugin_basename( __FILE__ );

        if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
            foreach($options['plugins'] as $each_plugin) {
                if ($each_plugin == $en_tforce_path_name) {
                    if (!function_exists('en_ups_freight_activate_hit_to_update_plan')) {
                        require_once(__DIR__ . '/update-plan.php');
                    }
                    
                    create_ups_freight_class();
                    create_ups_freight_wh_db();
                    create_ups_freight_option();
                    old_store_ups_ltl_dropship_status();
                    en_ups_freight_activate_hit_to_update_plan();
    
                    update_option('en_ups_ltl_update_now', $plugin_version);

                }
            }
        }
    }

    add_action( 'upgrader_process_complete', 'en_tforce_plugin_update',10, 2);

    /**
     * UPS FREIGHT Action And Filters
     */
    add_action('woocommerce_shipping_init', 'ups_freight_init');
    add_filter('woocommerce_shipping_methods', 'add_ups_freight');
    add_filter('woocommerce_get_settings_pages', 'ups_freight_shipping_sections');
    add_filter('woocommerce_package_rates', 'ups_freight_hide_shipping', 99);
    add_action('init', 'ups_freight_no_method_available');
    add_filter('woocommerce_cart_no_shipping_available_html', 'ups_freight_default_error_message');
    add_action('init', 'ups_freight_default_error_message_selection');
    add_action('init', 'ups_freight_default_error_message_selection');
    /**
     * UPS FREIGHT action links
     */
    add_filter('plugin_action_links', 'ups_freight_plugin_action', 10, 5);

    /**
     * Update Default custom error message selection
     */
    if (!function_exists("ups_freight_default_error_message_selection")) {

        function ups_freight_default_error_message_selection()
        {
            $custom_error_selection = get_option('wc_pervent_proceed_checkout_eniture');
            if (empty($custom_error_selection)) {
                update_option('wc_pervent_proceed_checkout_eniture', 'prevent', true);
                update_option('prevent_proceed_checkout_eniture', 'There are no shipping methods available for the address provided. Please check the address.', true);
            }
        }

    }

    /**
     * @param $message
     * @return string
     */
    if (!function_exists("ups_freight_default_error_message")) {

        function ups_freight_default_error_message($message)
        {

            if (get_option('wc_pervent_proceed_checkout_eniture') == 'prevent') {
                remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
                return __(get_option('prevent_proceed_checkout_eniture'));
            } else if (get_option('wc_pervent_proceed_checkout_eniture') == 'allow') {
                add_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
                return __(get_option('allow_proceed_checkout_eniture'));
            }
        }

    }

    /**
     * UPS Freight Plugin action
     * @staticvar type $plugin
     * @param $actions
     * @param $plugin_file
     * @return array
     */
    if (!function_exists("ups_freight_plugin_action")) {

        function ups_freight_plugin_action($actions, $plugin_file)
        {
            static $plugin;
            if (!isset($plugin))
                $plugin = plugin_basename(__FILE__);

            if ($plugin == $plugin_file) {
                $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=ups_freight_quotes">' . __('Settings', 'General') . '</a>');
                $site_link = array('support' => '<a href="https://support.eniture.com/" target="_blank">Support</a>');
                $actions = array_merge($settings, $actions);
                $actions = array_merge($site_link, $actions);
            }
            return $actions;
        }

    }
}

define("en_woo_plugin_ups_freight_quotes", "ups_freight_quotes");

add_action('wp_enqueue_scripts', 'en_ltl_ups_frontend_checkout_script');

/**
 * Load Frontend scripts for ODFL
 */
function en_ltl_ups_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_ltl_ups_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-ups-checkout.js', array(), '1.0.0');
    wp_localize_script('en_ltl_ups_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

/**
 * Get Domain Name
 */
if (!function_exists('ups_ltl_get_domain')) {

    function ups_ltl_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return getHost($url);
    }

}
if (!function_exists('getHost')) {

    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }

}

/**
 * Plans Common Hooks
 */
if (!function_exists("ups_freight_quotes_quotes_plans_suscription_and_features")) {

    function ups_freight_quotes_quotes_plans_suscription_and_features($feature)
    {
        $package = get_option('ups_freight_package');
        $features = array
        (
            'instore_pickup_local_devlivery' => array('3'),
            'hazardous_material' => array('2', '3'),
            'nested_material' => array('3'),
            // Cuttoff Time
            'ups_cutt_off_time' => array('2', '3'),
        );
        if (get_option('ups_freight_quotes_store_type') == "1") {
            $features['multi_warehouse'] = array('2', '3');
            $features['multi_dropship'] = array('', '0', '1', '2', '3');
        } else {
            $dropship_status = get_option('en_old_user_dropship_status');
            $warehouse_status = get_option('en_old_user_warehouse_status');
            $hazmat_status = get_option('en_old_user_hazmat_status');

            isset($dropship_status) && ($dropship_status == "0") ? $features['multi_dropship'] = array('', '0', '1', '2', '3') : '';
            isset($warehouse_status) && ($warehouse_status == "0") ? $features['multi_warehouse'] = array('2', '3') : '';
            isset($hazmat_status) && ($hazmat_status == "1") ? $features['hazardous_material'] = array('2', '3') : '';
        }

        return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
    }

    add_filter('ups_freight_quotes_quotes_plans_suscription_and_features', 'ups_freight_quotes_quotes_plans_suscription_and_features', 1);
}

if (!function_exists("ups_freight_quotes_plans_notification_link")) {

    function ups_freight_quotes_plans_notification_link($plans)
    {
        $plan = current($plans);
        $plan_to_upgrade = "";
        switch ($plan) {
            case 2:
                $plan_to_upgrade = "<a target='_blank' class='plan_color' href='https://eniture.com/woocommerce-tforce-ltl-freight/' target='_blank'>Standard Plan required.</a>";
                break;
            case 3:
                $plan_to_upgrade = "<a target='_blank' class='plan_color' href='https://eniture.com/woocommerce-tforce-ltl-freight/' target='_blank'>Advanced Plan required.</a>";
                break;
        }

        return $plan_to_upgrade;
    }

    add_filter('ups_freight_quotes_plans_notification_link', 'ups_freight_quotes_plans_notification_link', 1);
}

/**
 *
 * old customer check dropship / warehouse status on plugin update
 */
if (!function_exists("old_store_ups_ltl_dropship_status")) {

    function old_store_ups_ltl_dropship_status()
    {
        global $wpdb;

//      Check total no. of dropships on plugin updation
        $table_name = $wpdb->prefix . 'warehouse';
        $count_query = "select count(*) from $table_name where location = 'dropship' ";
        $num = $wpdb->get_var($count_query);

        if (get_option('en_old_user_dropship_status') == "0" && get_option('ups_freight_quotes_store_type') == "0") {
            $dropship_status = ($num > 1) ? 1 : 0;

            update_option('en_old_user_dropship_status', "$dropship_status");
        } elseif (get_option('en_old_user_dropship_status') == "" && get_option('ups_freight_quotes_store_type') == "0") {
            $dropship_status = ($num == 1) ? 0 : 1;

            update_option('en_old_user_dropship_status', "$dropship_status");
        }

//      Check total no. of warehouses on plugin updation
        $table_name = $wpdb->prefix . 'warehouse';
        $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
        $warehouse_num = $wpdb->get_var($warehouse_count_query);

        if (get_option('en_old_user_warehouse_status') == "0" && get_option('ups_freight_quotes_store_type') == "0") {
            $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

            update_option('en_old_user_warehouse_status', "$warehouse_status");
        } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('ups_freight_quotes_store_type') == "0") {
            $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

            update_option('en_old_user_warehouse_status', "$warehouse_status");
        }
    }

}

/*
 * Get Weight of Handling Unit
 */
if (!function_exists("ups_freight_return_weight_of_handling_unit")) {

    function ups_freight_return_weight_of_handling_unit($plugins)
    {

        $ups_freight_get_shipping_quotes_obj = new UPS_Freight_Get_Shipping_Quotes();
        $enabled_shipping_methods = $ups_freight_get_shipping_quotes_obj->get_enabled_shipping_methods();

        $current_plugin[] = [
            'name' => 'ups_freight',
            'handling_weight' => get_option('ups_freight_settings_handling_weight'),
        ];
        if (in_array('ups_freight', $enabled_shipping_methods)) {
            $plugins['ltl'] = (isset($plugins['ltl'])) ? array_merge($plugins['ltl'], $current_plugin) : $current_plugin;
        }
        return $plugins;
    }

    add_filter('en_weight_of_handling_unit', 'ups_freight_return_weight_of_handling_unit', 1, 999);
}
// fdo va
add_action('wp_ajax_nopriv_ups_fd', 'ups_fd_api');
add_action('wp_ajax_ups_fd', 'ups_fd_api');
/**
 * UPS AJAX Request
 */
function ups_fd_api()
{
    $store_name = ups_ltl_get_domain();
    $company_id = $_POST['company_id'];
    $data = [
        'plateform'  => 'wp',
        'store_name' => $store_name,
        'company_id' => $company_id,
        'fd_section' => 'tab=ups_freight_quotes&section=section-4',
    ];
    if (is_array($data) && count($data) > 0) {
        if($_POST['disconnect'] != 'disconnect') {
            $url =  'https://freightdesk.online/validate-company';
        }else {
            $url = 'https://freightdesk.online/disconnect-woo-connection';
        }
        $response = wp_remote_post($url, [
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $data,
            ]
        );
        $response = wp_remote_retrieve_body($response);
    }
    if($_POST['disconnect'] == 'disconnect') {
        $result = json_decode($response);
        if ($result->status == 'SUCCESS') {
            update_option('en_fdo_company_id_status', 0);
        }
    }
    echo $response;
    exit();
}
add_action('rest_api_init', 'en_rest_api_init_status_ups');
function en_rest_api_init_status_ups()
{
    register_rest_route('fdo-company-id', '/update-status', array(
        'methods' => 'POST',
        'callback' => 'en_ups_fdo_data_status',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Update FDO coupon data
 * @param array $request
 * @return array|void
 */
function en_ups_fdo_data_status(WP_REST_Request $request)
{
    $status_data = $request->get_body();
    $status_data_decoded = json_decode($status_data);
    if (isset($status_data_decoded->connection_status)) {
        update_option('en_fdo_company_id_status', $status_data_decoded->connection_status);
        update_option('en_fdo_company_id', $status_data_decoded->fdo_company_id);
    }
    return true;
}

add_filter('en_suppress_parcel_rates_hook', 'supress_parcel_rates');
if (!function_exists('supress_parcel_rates')) {
    function supress_parcel_rates() {
        $exceedWeight = get_option('en_plugins_return_LTL_quotes') == 'yes';
        $supress_parcel_rates = get_option('en_suppress_parcel_rates') == 'suppress_parcel_rates';
        return ($exceedWeight && $supress_parcel_rates);
    }
}