<?php
/**
 * Class UPS_Freight_Woo_Update_Changes
 * @package     UPS Freight Quotes
 * @author      Eniture-Technology
 */
    if ( ! defined( 'ABSPATH' ) ) {
        exit; 
    }
    
    
/**
 * UPS Freight WooCommerce Class for new and old functions
 */

    class UPS_Freight_Woo_Update_Changes 
    {
        /**
         * WooVersion
         */
        public $WooVersion;
        /**
         * Constructor
         */
        function __construct() 
        {
            if (!function_exists('get_plugins'))
               require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            $plugin_folder     = get_plugins('/' . 'woocommerce');
            $plugin_file       = 'woocommerce.php';
            $this->WooVersion  = $plugin_folder[$plugin_file]['Version'];
            
        }
        /**
         * UPS Freight Postcode
         */
        function ups_freight_postcode()
        { 
            $sPostCode = "";
            switch ($this->WooVersion) 
            {  
                case ($this->WooVersion <= '2.7'):
                    $sPostCode = WC()->customer->get_postcode();
                    break;
                case ($this->WooVersion >= '3.0'):
                    $sPostCode = WC()->customer->get_billing_postcode();
                    break;

                default:
                    break;
            }
            return $sPostCode;
        }
        /**
         * UPS Freight State
         */
        function ups_freight_getState()
        { 
            $sState = "";
            switch ($this->WooVersion) 
            {  
                case ($this->WooVersion <= '2.7'):
                    $sState = WC()->customer->get_state();
                    break;
                case ($this->WooVersion >= '3.0'):
                    $sState = WC()->customer->get_billing_state();
                    break;

                default:
                    break;
            }
            return $sState;
        }
        /**
         * UPS Freight City
         */
        function ups_freight_getCity()
        { 
            $sCity = "";
            switch ($this->WooVersion) 
            {  
                case ($this->WooVersion <= '2.7'):
                    $sCity = WC()->customer->get_city();
                    break;
                case ($this->WooVersion >= '3.0'):
                    $sCity = WC()->customer->get_billing_city();
                    break;

                default:
                    break;
            }
            return $sCity;
        }
        /**
         * UPS Freight Country
         */
        function ups_freight_getCountry()
        { 
            $sCountry = "";
            switch ($this->WooVersion) 
            {  
                case ($this->WooVersion <= '2.7'):
                    $sCountry = WC()->customer->get_country();
                    break;
                case ($this->WooVersion >= '3.0'):
                    $sCountry = WC()->customer->get_billing_country();
                    break;

                default:
                    break;
            }
            return $sCountry;
        }
    }