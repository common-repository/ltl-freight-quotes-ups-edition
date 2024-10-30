jQuery(document).ready(function () {

    // Weight threshold for LTL freight
    en_weight_threshold_limit();

    jQuery("#order_shipping_line_items .shipping .display_meta").css('display', 'none');

    var prevent_text_box = jQuery('.prevent_text_box').length;
    if (!prevent_text_box > 0) {
        jQuery("input[name*='wc_pervent_proceed_checkout_eniture']").closest('tr').addClass('wc_pervent_proceed_checkout_eniture');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after('Allow user to continue to check out and display this message <br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_ups_freight_admin_script.allow_proceed_checkout_eniture + '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='prevent']").after('Prevent user from checking out and display this message <br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_ups_freight_admin_script.prevent_proceed_checkout_eniture + '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
    }

    jQuery("#ups_freight_setting_residential").closest('tr').addClass("ups_freight_setting_residential");
    jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");
    jQuery("#avaibility_lift_gate").closest('tr').addClass("avaibility_lift_gate");
    jQuery("#ups_freight_settings_liftgate").closest('tr').addClass("ups_freight_settings_liftgate");
    jQuery("#ups_freight_quotes_liftgate_delivery_as_option").closest('tr').addClass("ups_freight_quotes_liftgate_delivery_as_option");
    // jQuery("#ups_freight_relation_to_shipper").closest('tr').addClass("shipper_and_thirdparty_field");
    jQuery("#ups_freight_thirdparty_country_or_territory").closest('tr').addClass("shipper_and_thirdparty_field");
    jQuery("#ups_freight_thirdparty_postal_code").closest('tr').addClass("shipper_and_thirdparty_field");
    jQuery("#ups_freight_payerCity").closest('tr').addClass("shipper_and_thirdparty_field_hidden_fields");
    jQuery("#ups_freight_payerState").closest('tr').addClass("shipper_and_thirdparty_field_hidden_fields");
    jQuery(".shipper_and_thirdparty_field_hidden_fields").hide();
    jQuery("#ups_freight_settings_handling_weight").closest('tr').addClass("ups_freight_settings_handling_weight_tr");
    jQuery("#ups_freight_maximum_handling_weight").closest('tr').addClass("ups_freight_maximum_handling_weight_tr");
    jQuery("#en_weight_threshold_lfq").closest('tr').addClass("en_weight_threshold_lfq_tr");
    jQuery("#ups_freight_setting_label_as").closest('tr').addClass("ups_freight_setting_label_as_tr");
    jQuery("#ups_freight_settings_handling_fee").closest('tr').addClass("ups_freight_settings_handling_fee_tr");
    jQuery("#ups_freight_relation_to_shipper").closest('tr').addClass("ups_freight_relation_to_shipper_tr");
    jQuery("#ups_freight_settings_allow_other_plugins").closest('tr').addClass("ups_freight_settings_allow_other_plugins_tr");
    jQuery('#en_weight_threshold_lfq').attr('maxlength', 4);

    jQuery("#ups_freight_settings_handling_weight , #ups_freight_settings_handling_fee, #en_weight_threshold_lfq").focus(function (e) {
        jQuery("#" + this.id).css({'border-color': '#ddd'});
    });
    // Cuttoff Time
    jQuery("#ups_freight_shipment_offset_days").closest('tr').addClass("ups_freight_shipment_offset_days_tr");
    jQuery("#all_shipment_days_ups").closest('tr').addClass("all_shipment_days_ups_tr");
    jQuery(".ups_shipment_day").closest('tr').addClass("ups_shipment_day_tr");
    jQuery("#ups_freight_order_cut_off_time").closest('tr').addClass("ups_freight_cutt_off_time_ship_date_offset");
    var ups_current_time = en_ups_freight_admin_script.ups_freight_order_cutoff_time;
    if (ups_current_time == '') {

        jQuery('#ups_freight_order_cut_off_time').wickedpicker({
            now: '',
            title: 'Cut Off Time',
        });
    } else {
        jQuery('#ups_freight_order_cut_off_time').wickedpicker({

            now: ups_current_time,
            title: 'Cut Off Time'
        });
    }

    var delivery_estimate_val = jQuery('input[name=ups_delivery_estimates]:checked').val();
    if (delivery_estimate_val == 'dont_show_estimates') {
        jQuery("#ups_freight_order_cut_off_time").prop('disabled', true);
        jQuery("#ups_freight_shipment_offset_days").prop('disabled', true);
        jQuery("#ups_freight_shipment_offset_days").css("cursor", "not-allowed");
        jQuery("#ups_freight_order_cut_off_time").css("cursor", "not-allowed");
        jQuery('.ups_shipment_day, .all_shipment_days_ups').prop('disabled', true);
        jQuery('.ups_shipment_day, .all_shipment_days_ups').css("cursor", "not-allowed");
    } else {
        jQuery("#ups_freight_order_cut_off_time").prop('disabled', false);
        jQuery("#ups_freight_shipment_offset_days").prop('disabled', false);
        // jQuery("#ups_freight_order_cut_off_time").css("cursor", "auto");
        jQuery("#ups_freight_order_cut_off_time").css("cursor", "");
        jQuery('.ups_shipment_day, .all_shipment_days_ups').prop('disabled', false);
        jQuery('.ups_shipment_day, .all_shipment_days_ups').css("cursor", "");
    }

    jQuery("input[name=ups_delivery_estimates]").change(function () {
        var delivery_estimate_val = jQuery('input[name=ups_delivery_estimates]:checked').val();
        if (delivery_estimate_val == 'dont_show_estimates') {
            jQuery("#ups_freight_order_cut_off_time").prop('disabled', true);
            jQuery("#ups_freight_shipment_offset_days").prop('disabled', true);
            jQuery("#ups_freight_order_cut_off_time").css("cursor", "not-allowed");
            jQuery("#ups_freight_shipment_offset_days").css("cursor", "not-allowed");
            jQuery('.ups_shipment_day, .all_shipment_days_ups').prop('disabled', true);
            jQuery('.ups_shipment_day, .all_shipment_days_ups').css("cursor", "not-allowed");
        } else {
            jQuery("#ups_freight_order_cut_off_time").prop('disabled', false);
            jQuery("#ups_freight_shipment_offset_days").prop('disabled', false);
            jQuery("#ups_freight_order_cut_off_time").css("cursor", "auto");
            jQuery("#ups_freight_shipment_offset_days").css("cursor", "auto");
            jQuery('.ups_shipment_day, .all_shipment_days_ups').prop('disabled', false);
            jQuery('.ups_shipment_day, .all_shipment_days_ups').css("cursor", "auto");
        }
    });

    /*
     * Uncheck Week days Select All Checkbox
     */
    jQuery(".ups_shipment_day").on('change load', function () {

        var checkboxes = jQuery('.ups_shipment_day:checked').length;
        var un_checkboxes = jQuery('.ups_shipment_day').length;
        if (checkboxes === un_checkboxes) {
            jQuery('.all_shipment_days_ups').prop('checked', true);
        } else {
            jQuery('.all_shipment_days_ups').prop('checked', false);
        }
    });

    /*
     * Select All Shipment Week days
     */

    var all_int_checkboxes = jQuery('.all_shipment_days_ups');
    if (all_int_checkboxes.length === all_int_checkboxes.filter(":checked").length) {
        jQuery('.all_shipment_days_ups').prop('checked', true);
    }

    jQuery(".all_shipment_days_ups").change(function () {
        if (this.checked) {
            jQuery(".ups_shipment_day").each(function () {
                this.checked = true;
            });
        } else {
            jQuery(".ups_shipment_day").each(function () {
                this.checked = false;
            });
        }
    });


    //** End: Order Cut Off Time
    jQuery("#en_weight_threshold_lfq").keydown(function (e) {
        // Allow: backspace, delete, tab, escape and enter
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }

        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });
    //**Start: Shipper and ThirdParty fields
    jQuery(".shipper_and_thirdparty_field").hide();
    jQuery("#ups_freight_relation_to_shipper").change(function () {
        var relation_to_shipper = jQuery(this).val();
        if (relation_to_shipper == 'thirdparty') {
            jQuery(".shipper_and_thirdparty_field").show();
        } else {
            jQuery(".shipper_and_thirdparty_field").hide();
        }
    });
    jQuery("#ups_freight_thirdparty_postal_code").change(function () {
        fedex_small_get_address();

    });
    jQuery(window).on('load', function () {
        var relation_to_shipper = jQuery("#ups_freight_relation_to_shipper").val();
        if (relation_to_shipper == 'thirdparty') {
            jQuery(".shipper_and_thirdparty_field").show();
        }
    });

    jQuery('#ups_freight_thirdparty_postal_code').on('keydown', function (e) {

        var alphanumers = /^[a-zA-Z0-9]+$/;
        if (!alphanumers.test(e.key)) {
            e.preventDefault();
        }
    });

    /**
     *Weight of Handling Unit field validation
     */
    jQuery("#ups_freight_settings_handling_weight,#ups_freight_maximum_handling_weight").keydown(function (e) {
        // Allow: backspace, delete, tab, escape and enter
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) ||
            (e.target.id == 'ups_freight_settings_handling_weight' && (e.keyCode == 109)) ||
            (e.target.id == 'ups_freight_settings_handling_weight' && (e.keyCode == 189))) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 3)) {
            if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                e.preventDefault();
            }
        }

    });
    
    /**
     *Handling Fee / Markup field validation
     */
    jQuery("#ups_freight_settings_handling_fee , #en_wd_origin_markup, #en_wd_dropship_markup, ._en_product_markup").keydown(function (e) {

        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
            if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                e.preventDefault();
            }
        }
        
        if(jQuery(this).val().length > 7){
            e.preventDefault();
        }

    });

    jQuery("#ups_freight_settings_handling_fee, #en_wd_origin_markup, #en_wd_dropship_markup, ._en_product_markup").keyup(function (e) {

        var val = jQuery(this).val();

        if (val.split('.').length - 1 > 1) {

            var newval = val.substring(0, val.length - 1);
            var countDots = newval.substring(newval.indexOf('.') + 1).length;
            newval = newval.substring(0, val.length - countDots - 1);
            jQuery(this).val(newval);
        }

        if (val.split('%').length - 1 > 1) {
            var newval = val.substring(0, val.length - 1);
            var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
            newval = newval.substring(0, val.length - countPercentages - 1);
            jQuery(this).val(newval);
        }
        if (val.split('>').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
            newval = newval.substring(newval, newval.length - countGreaterThan - 1);
            jQuery(this).val(newval);
        }
        if (val.split('_').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
            newval = newval.substring(newval, newval.length - countUnderScore - 1);
            jQuery(this).val(newval);
        }
    });
    /**
     * Offer lift gate delivery as an option and Always include residential delivery fee
     * @returns {undefined}
     */

    jQuery(".checkbox_fr_add").on("click", function () {
        var id = jQuery(this).attr("id");
        if (id == "ups_freight_settings_liftgate") {
            jQuery("#ups_freight_quotes_liftgate_delivery_as_option").prop({checked: false});
            jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false});

        } else if (id == "ups_freight_quotes_liftgate_delivery_as_option" ||
            id == "en_woo_addons_liftgate_with_auto_residential") {
            jQuery("#ups_freight_settings_liftgate").prop({checked: false});
        }
    });


    var url = getUrlVarsUPSFreight()["tab"];
    if (url === 'ups_freight_quotes') {
        jQuery('#footer-left').attr('id', 'wc-footer-left');
    }

    /**
     * Add err class on connection settings page
     */
    jQuery('.ups_freight_tab_section input[type="text"]').each(function () {
        if (jQuery(this).parent().find('.err').length < 1) {
            jQuery(this).after('<span class="err"></span>');
        }
    });

    /**
     * Show Note Message on Connection Settings Page
     */
    if (jQuery("#ups_freight_setting_username").length > 0) {
        jQuery('.ups_freight_tab_section .form-table').before("<div class='warning-msg'><p>Note! You must have an LTL Freight enabled TForce account to use this application. If you do not have one, call 800-333-7400, or <a href='https://www.ups.com/one-to-one/login' target='_blank' >register</a> online.</p></div>");
    }

    /**
     * Add maxlength Attribute on Handling Fee Quote Setting Page
     */

    jQuery("#ups_freight_settings_handling_fee, #en_wd_origin_markup, #en_wd_dropship_markup, ._en_product_markup ").attr('maxlength', '8');

    /**
     * Add maxlength Attribute on Account Number Connection Setting Page
     */

    jQuery("#ups_freight_setting_account_no").attr('maxlength', '8');
    jQuery("#ups_freight_setting_account_no").data('optional', '1');

    jQuery("#ups_freight_client_id").attr('minlength', '1');
    jQuery("#ups_freight_client_secret").attr('minlength', '1');
    jQuery("#ups_freight_client_id").attr('maxlength', '100');
    jQuery("#ups_freight_client_secret").attr('maxlength', '100');
    jQuery("#ups_freight_new_api_username").attr('maxlength', '100');
    jQuery("#ups_freight_new_api_password").attr('maxlength', '100');


    /**
     * Add Title To Connection Setting Fields
     */

    jQuery('#ups_freight_setting_account_no').attr('title', 'Account Number');
    jQuery('#ups_freight_setting_username').attr('title', 'Username');
    jQuery('#ups_freight_setting_password').attr('title', 'Password');
    jQuery('#ups_freight_setting_acccess_key').attr('title', 'API Access Key');
    jQuery('#ups_freight_setting_licnse_key').attr('title', 'Eniture API Key');

    jQuery('#ups_freight_client_id').attr('title', 'Client ID');
    jQuery('#ups_freight_client_secret').attr('title', 'Client Secret');
    jQuery('#ups_freight_new_api_username').attr('title', 'Username');
    jQuery('#ups_freight_new_api_password').attr('title', 'Password');

    /**
     * Add Title To Quote Setting Fields
     */

    jQuery('#ups_freight_setting_label_as').attr('title', 'Label As');
    jQuery('#ups_freight_setting_label_as').attr('maxlength', '50');
    jQuery('#ups_freight_settings_handling_fee').attr('title', 'Handling Fee / Markup');

    jQuery(".ups_freight_tab_section .button-primary, .ups_freight_tab_section .is-primary").click(function () {

        var input = validateInput('.ups_freight_tab_section');
        if (input === false) {
            return false;
        }

    });

    /**
     * Save Changes At Quote Section Action
     */

    jQuery('.ups_freight_quote_section .button-primary, .ups_freight_quote_section .is-primary').on('click', function () {

        jQuery(".updated").hide();
        jQuery('.error').remove();

        var quote_setting_postal_code = jQuery('#ups_freight_thirdparty_postal_code').val();
        var ups_freight_payerCity = jQuery('#ups_freight_payerCity').val();
        var ups_freight_payerState = jQuery('#ups_freight_payerState').val();

        if (!ups_freight_weight_of_handling_unit()) {
            return false;
        }
        if (!ups_freight_max_weight_of_handling_unit()) {
            return false;
        }
        if (!ups_freight_weight_of_threshold()) {
            return false;
        }
        if (quote_setting_postal_code != '' && quote_setting_postal_code.length > 6) {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_quote_setting_zip"><p><strong>Error! </strong>Postal Code should be less than 6.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_quote_setting_zip').position().top
            });
            return false;
        }
        if (ups_freight_payerCity != '' && ups_freight_payerCity.length > 100) {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_payerCity"><p><strong>Error! </strong>Payer City Length should be less than 100 characters.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_payerCity').position().top
            });
            return false;
        }
        if (ups_freight_payerState != '' && ups_freight_payerState.length > 2) {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_payerState"><p><strong>Error! </strong>Payer City State Code should be of 2 characters.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_payerState').position().top
            });
            return false;
        }
        /*Custom Error Message Validation*/
        var checkedValCustomMsg = jQuery("input[name='wc_pervent_proceed_checkout_eniture']:checked").val();
        var allow_proceed_checkout_eniture = jQuery("textarea[name=allow_proceed_checkout_eniture]").val();
        var prevent_proceed_checkout_eniture = jQuery("textarea[name=prevent_proceed_checkout_eniture]").val();

        if (checkedValCustomMsg == 'allow' && allow_proceed_checkout_eniture == '') {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_custom_error_message').position().top
            });
            return false;
        } else if (checkedValCustomMsg == 'prevent' && prevent_proceed_checkout_eniture == '') {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_custom_error_message').position().top
            });
            return false;
        }

        var handling_fee = jQuery('#ups_freight_settings_handling_fee').val();

        if (isValidNumber(handling_fee) == '-') {
            jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.ups_freight_handlng_fee_error').position().top
            });
            jQuery("#ups_freight_settings_handling_fee").css({'border-color': '#e81123'});
            return false;
        }


        if (handling_fee.slice(handling_fee.length - 1) == '%') {
            handling_fee = handling_fee.slice(0, handling_fee.length - 1);
        }

        if (handling_fee === "") {
            return true;
        } else {
            if (isValidNumber(handling_fee) === false) {
                jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.ups_freight_handlng_fee_error').position().top
                });
                jQuery("#ups_freight_settings_handling_fee").css({'border-color': '#e81123'});
                return false;
            } else if (isValidNumber(handling_fee) === 'decimal_point_err') {
                jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10% and only 2 digits are allowed after decimal point.</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.ups_freight_handlng_fee_error').position().top
                });
                jQuery("#ups_freight_settings_handling_fee").css({'border-color': '#e81123'});
                return false;
            } else {
                return true;
            }
        }
    });

    if (typeof ups_freight_connection_section_api_endpoint == 'function') {
        ups_freight_connection_section_api_endpoint();
    }

    jQuery('#ups_freight_api_endpoint').on('change', function () {
        ups_freight_connection_section_api_endpoint();
    });


    /**
     * Test connection
     */

    jQuery(".ups_freight_tab_section .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary ups_freight_test_connection">Test connection</a>');
    jQuery('.ups_freight_test_connection').click(function (e) {

        var input = validateInput('.ups_freight_tab_section');
        if (input === false) {
            return false;
        }

        let api_endpoint = jQuery('#ups_freight_api_endpoint').val();
        var postForm = {
            'action': 'ups_freight_test_connection',
            'ups_freight_acc_no': jQuery('#ups_freight_setting_account_no').val(),
            'ups_freight_license': jQuery('#ups_freight_setting_licnse_key').val(),
            'api_end_point': api_endpoint,
        };
        if(api_endpoint == 'ups_old_api'){
            postForm.ups_freight_username = jQuery('#ups_freight_setting_username').val();
            postForm.ups_freight_password = jQuery('#ups_freight_setting_password').val();
            postForm.ups_freight_key = jQuery('#ups_freight_setting_acccess_key').val();
        }else{
            postForm.client_id = jQuery('#ups_freight_client_id').val();
            postForm.client_secret = jQuery('#ups_freight_client_secret').val();
            postForm.ups_freight_username = jQuery('#ups_freight_new_api_username').val();
            postForm.ups_freight_password = jQuery('#ups_freight_new_api_password').val();
        }

        
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',

            beforeSend: function () {
                jQuery('#ups_freight_setting_account_no').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_setting_username').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_setting_password').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_setting_acccess_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_setting_licnse_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_client_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_client_secret').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_new_api_username').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#ups_freight_new_api_password').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data) {
                jQuery('#message').hide();
                jQuery(".ups_freight_error_message").remove();
                jQuery(".ups_freight_success_message").remove();

                jQuery('#ups_freight_setting_account_no').css('background', '#fff');
                jQuery('#ups_freight_setting_username').css('background', '#fff');
                jQuery('#ups_freight_setting_password').css('background', '#fff');
                jQuery('#ups_freight_setting_acccess_key').css('background', '#fff');
                jQuery('#ups_freight_setting_licnse_key').css('background', '#fff');
                jQuery('#ups_freight_client_id').css('background', '#fff');
                jQuery('#ups_freight_client_secret').css('background', '#fff');
                jQuery('#ups_freight_new_api_username').css('background', '#fff');
                jQuery('#ups_freight_new_api_password').css('background', '#fff');

                if (data.message === "success") {
                    jQuery('.warning-msg').before('<div class="notice notice-success ups_freight_success_message"><p><strong>Success!</strong> The test resulted in a successful connection.</p></div>');
                } else if (data.message !== "failure" && data.message !== "success") {
                    jQuery('.warning-msg').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error!</strong>  ' + data.message + ' </p></div>');
                } else {
                    jQuery('.warning-msg').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error!</strong> Please verify credentials and try again.</p></div>');
                }
            }
        });
        e.preventDefault();
    });
    // fdo va
    jQuery('#fd_online_id_ups').click(function (e) {
        var postForm = {
            'action': 'ups_fd',
            'company_id': jQuery('#freightdesk_online_id').val(),
            'disconnect': jQuery('#fd_online_id_ups').attr("data")
        }
        var id_lenght = jQuery('#freightdesk_online_id').val();
        var disc_data = jQuery('#fd_online_id_ups').attr("data");
        if(typeof (id_lenght) != "undefined" && id_lenght.length < 1) {
            jQuery(".ups_freight_error_message").remove();
            jQuery('.user_guide_fdo').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error!</strong> FreightDesk Online ID is Required.</p></div>');
            return;
        }
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: postForm,
            beforeSend: function () {
                jQuery('#freightdesk_online_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data_response) {
                if(typeof (data_response) == "undefined"){
                    return;
                }
                var fd_data = JSON.parse(data_response);
                jQuery('#freightdesk_online_id').css('background', '#fff');
                jQuery(".ups_freight_error_message").remove();
                if((typeof (fd_data.is_valid) != 'undefined' && fd_data.is_valid == false) || (typeof (fd_data.status) != 'undefined' && fd_data.is_valid == 'ERROR')) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'SUCCESS') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-success ups_freight_success_message"><p><strong>Success! ' + fd_data.message + '</strong></p></div>');
                    window.location.reload(true);
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'ERROR') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if (fd_data.is_valid == 'true') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error!</strong> FreightDesk Online ID is not valid.</p></div>');
                } else if (fd_data.is_valid == 'true' && fd_data.is_connected) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error ups_freight_error_message"><p><strong>Error!</strong> Your store is already connected with FreightDesk Online.</p></div>');

                } else if (fd_data.is_valid == true && fd_data.is_connected == false && fd_data.redirect_url != null) {
                    window.location = fd_data.redirect_url;
                } else if (fd_data.is_connected == true) {
                    jQuery('#con_dis').empty();
                    jQuery('#con_dis').append('<a href="#" id="fd_online_id_ups" data="disconnect" class="button-primary">Disconnect</a>')
                }
            }
        });
        e.preventDefault();
    });

    // JS for edit product nested fields
    jQuery("._nestedMaterials").closest('p').addClass("_nestedMaterials_tr");
    jQuery("._nestedPercentage").closest('p').addClass("_nestedPercentage_tr");
    jQuery("._maxNestedItems").closest('p').addClass("_maxNestedItems_tr");
    jQuery("._nestedDimension").closest('p').addClass("_nestedDimension_tr");
    jQuery("._nestedStakingProperty").closest('p').addClass("_nestedStakingProperty_tr");

    if (!jQuery('._nestedMaterials').is(":checked")) {
        jQuery('._nestedPercentage_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._maxNestedItems_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._nestedStakingProperty_tr').hide();
    } else {
        jQuery('._nestedPercentage_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._maxNestedItems_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._nestedStakingProperty_tr').show();
    }

    jQuery("._nestedPercentage").attr('min', '0');
    jQuery("._maxNestedItems").attr('min', '0');
    jQuery("._nestedPercentage").attr('max', '100');
    jQuery("._maxNestedItems").attr('max', '100');
    jQuery("._nestedPercentage").attr('maxlength', '3');
    jQuery("._maxNestedItems").attr('maxlength', '3');

    if (jQuery("._nestedPercentage").val() == '') {
        jQuery("._nestedPercentage").val(0);
    }

    // Nested fields validation on product details
    jQuery("._nestedPercentage").keydown(function (eve) {
        ups_lfq_stop_special_characters(eve);
        var nestedPercentage = jQuery('._nestedPercentage').val();
        if (nestedPercentage.length == 2) {
            var newValue = nestedPercentage + '' + eve.key;
            if (newValue > 100) {
                return false;
            }
        }
    });

    jQuery("._maxNestedItems").keydown(function (eve) {
        ups_lfq_stop_special_characters(eve);
    });

    jQuery("._nestedMaterials").change(function () {
        if (!jQuery('._nestedMaterials').is(":checked")) {
            jQuery('._nestedPercentage_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._maxNestedItems_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._nestedStakingProperty_tr').hide();
        } else {
            jQuery('._nestedPercentage_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._maxNestedItems_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._nestedStakingProperty_tr').show();
        }
    });
    
    // Product variants settings
    jQuery(document).on("click", '._nestedMaterials', function(e) {
        const checkbox_class = jQuery(e.target).attr("class");
        const name = jQuery(e.target).attr("name");
        const checked = jQuery(e.target).prop('checked');

        if (checkbox_class?.includes('_nestedMaterials')) {
            const id = name?.split('_nestedMaterials')[1];
            setNestMatDisplay(id, checked);
        }
    });

    // Callback function to execute when mutations are observed
    const handleMutations = (mutationList) => {
        let childs = [];
        for (const mutation of mutationList) {
            childs = mutation?.target?.children;
            if (childs?.length) setNestedMaterialsUI();
          }
    };
    const observer = new MutationObserver(handleMutations),
        targetNode = document.querySelector('.woocommerce_variations.wc-metaboxes'),
        config = { childList: true, subtree: true };
    if (targetNode) observer.observe(targetNode, config);
});

// Weight threshold for LTL freight
if (typeof en_weight_threshold_limit != 'function') {
    function en_weight_threshold_limit() {
        // Weight threshold for LTL freight
        jQuery("#en_weight_threshold_lfq").keypress(function (e) {
            if (String.fromCharCode(e.keyCode).match(/[^0-9]/g) || !jQuery("#en_weight_threshold_lfq").val().match(/^\d{0,3}$/)) return false;
        });

        jQuery('#en_plugins_return_LTL_quotes').on('change', function () {
            if (jQuery('#en_plugins_return_LTL_quotes').prop("checked")) {
                jQuery('tr.en_weight_threshold_lfq').css('display', 'contents');
            } else {
                jQuery('tr.en_weight_threshold_lfq').css('display', 'none');
            }
        });

        jQuery("#en_plugins_return_LTL_quotes").closest('tr').addClass("en_plugins_return_LTL_quotes_tr");
        // Weight threshold for LTL freight
        var weight_threshold_class = jQuery("#en_weight_threshold_lfq").attr("class");
        jQuery("#en_weight_threshold_lfq").closest('tr').addClass("en_weight_threshold_lfq " + weight_threshold_class);

        // Weight threshold for LTL freight is empty
        if (jQuery('#en_weight_threshold_lfq').length && !jQuery('#en_weight_threshold_lfq').val().length > 0) {
            jQuery('#en_weight_threshold_lfq').val(150);
        }
    }
}

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
function getUrlVarsUPSFreight() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

/**
 * Validate Input If Empty or Invalid
 */

function validateInput(form_id) {
    var has_err = true;
    jQuery(form_id + " input[type='text']").each(function () {

        var input = jQuery(this).val();
        var response = validateString(input);
        var errorText = jQuery(this).attr('title');
        var optional = jQuery(this).data('optional');

        var errorElement = jQuery(this).parent().find('.err');
        jQuery(errorElement).html('');

        optional = (optional === undefined) ? 0 : 1;
        errorText = (errorText != undefined) ? errorText : '';

        if ((optional == 0) && (response == false || response == 'empty')) {
            errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';
            jQuery(errorElement).html(errorText);
        }
        has_err = (response != true && optional == 0) ? false : has_err;
    });
    return has_err;
}

/**
 * Check Input Value Is Not String
 */

function isValidNumber(value, noNegative) {
    if (typeof (noNegative) === 'undefined')
        noNegative = false;
    var isValidNumber = false;
    var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;

    if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
        if (value.indexOf(".") >= 0) {
            var n = value.split(".");
            if (n[n.length - 1].length <= 4) {
                isValidNumber = true;
            } else {
                isValidNumber = 'decimal_point_err';
            }
        } else {
            isValidNumber = true;
        }
    }
    return isValidNumber;
}

/**
 * Validate Input String
 */

function validateString(string) {
    if (string == '')
        return 'empty';
    else
        return true;

}


function fedex_small_get_address() {
    if (jQuery("#ups_freight_thirdparty_postal_code").val() == '') {
        return false;
    }
    var postForm = {
        'action': 'en_wd_get_address',
        'origin_zip': jQuery('#ups_freight_thirdparty_postal_code').val(),
    };

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: postForm,
        dataType: 'json',
        beforeSend: function () {
            jQuery('#ups_freight_thirdparty_postal_code').css('background', 'rgba(255, 255, 255, 1) url("' + en_ups_freight_admin_script.plugins_url + '/ltl-freight-quotes-ups-edition/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');

            jQuery('.woocommerce-save-button').attr('disabled', true);

        },
        success: function (data) {
            jQuery('#ups_freight_thirdparty_postal_code').css('background', '#fff');
            jQuery('.woocommerce-save-button').attr('disabled', false);

            if (data) {
                if (data.country === 'US' || data.country === 'CA' || data.country === 'GU' || data.country === 'MX' || data.country === 'PR' || data.country === 'PR') {
                    if (data.postcode_localities == 1) {

                        jQuery('.city_select').show();
                        jQuery('#actname').replaceWith(data.city_option);
                        jQuery('.en_wd_multi_state').replaceWith(data.city_option);
                        jQuery('.city-multiselect').change(function () {
                            setCity(this);//$this
                        });
                        jQuery('#ups_freight_payerCity').val(data.first_city);
                        jQuery('#ups_freight_payerState').val(data.state);

                        jQuery('.city_input').hide();
                    } else {

                        jQuery('.city_input').show();
                        jQuery('#_city').removeAttr('value');
                        jQuery('.city_select').hide();
                        jQuery('#ups_freight_payerCity').val(data.city);
                        jQuery('#ups_freight_payerState').val(data.state);

                    }
                } else {
                    jQuery('.not_allowed').show('slow');
                    jQuery('#ups_freight_payerCity').css('background', 'none');
                    jQuery('#ups_freight_payerState').css('background', 'none');
                    jQuery('#en_wd_origin_country').css('background', 'none');
                    setTimeout(function () {
                        jQuery('.not_allowed').hide('slow');
                    }, 5000);
                }
            }
        },
    });
    return false;
}

function ups_lfq_stop_special_characters(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        e.preventDefault();
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186 && e.keyCode != 8) {
        e.preventDefault();
    }
    if (e.keyCode == 186 || e.keyCode == 190 || e.keyCode == 189 || (e.keyCode > 64 && e.keyCode < 91)) {
        e.preventDefault();
        return;
    }
}

function ups_freight_weight_of_threshold() {
    var weight_of_threshold = jQuery('#en_weight_threshold_lfq').val();
    var weight_of_threshold_regex = /^[0-9]*$/;
    if (weight_of_threshold != '' && !weight_of_threshold_regex.test(weight_of_threshold)) {
        jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_wieght_of_threshold_error"><p><strong>Error! </strong>Cart weight threshold format should be like 150.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.ups_freight_wieght_of_threshold_error').position().top
        });
        jQuery("#en_weight_threshold_lfq").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function ups_freight_max_weight_of_handling_unit() {
    var max_weight_of_handling_unit = jQuery('#ups_freight_maximum_handling_weight').val();
    if (max_weight_of_handling_unit.length > 0) {
        var validResponse = isValidDecimal(max_weight_of_handling_unit, 'ups_freight_maximum_handling_weight');
    } else {
        validResponse = true;
    }
    if (validResponse) {
        return true;
    } else {
        jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_max_wieght_of_handling_unit_error"><p><strong>Error! </strong>Maximum Weight per Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.ups_freight_max_wieght_of_handling_unit_error').position().top
        });
        jQuery("#ups_freight_maximum_handling_weight").css({'border-color': '#e81123'});
        return false;
    }
}

function ups_freight_weight_of_handling_unit() {
    var weight_of_handling_unit = jQuery('#ups_freight_settings_handling_weight').val();
    if (weight_of_handling_unit.length > 0) {
        var validResponse = isValidDecimal(weight_of_handling_unit, 'ups_freight_settings_handling_weight');
    } else {
        validResponse = true;
    }
    if (validResponse) {
        return true;
    } else {
        jQuery("#mainform .ups_freight_quote_section").prepend('<div id="message" class="error inline ups_freight_wieght_of_handling_unit_error"><p><strong>Error! </strong>Weight of Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.ups_freight_wieght_of_handling_unit_error').position().top
        });
        jQuery("#ups_freight_settings_handling_weight").css({'border-color': '#e81123'});
        return false;
    }
}

/**
 * Check is valid number
 * @param num
 * @param selector
 * @param limit | LTL weight limit 20K
 * @returns {boolean}
 */
function isValidDecimal(num, selector, limit = 20000) {
    // validate the number:
    // positive and negative numbers allowed
    // just - sign is not allowed,
    // -0 is also not allowed.
    if (parseFloat(num) === 0) {
        // Change the value to zero
        return false;
    }

    const reg = /^(-?[0-9]{1,5}(\.\d{1,4})?|[0-9]{1,5}(\.\d{1,4})?)$/;
    let isValid = false;
    if (reg.test(num)) {
        isValid = inRange(parseFloat(num), -limit, limit);
    }
    if (isValid === true) {
        return true;
    }
    return isValid;
}

/**
 * Check is the number is in given range
 *
 * @param num
 * @param min
 * @param max
 * @returns {boolean}
 */
function inRange(num, min, max) {
    return ((num - min) * (num - max) <= 0);
}

// Update plan
if (typeof en_update_plan != 'function') {
    function en_update_plan(input) {
        let action = jQuery(input).attr('data-action');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: action},
            success: function (data_response) {
                window.location.reload(true);
            }
        });
    }
}

/**
 * Hide and show test connection fields based on API selection
 */
function ups_freight_connection_section_api_endpoint() {
    jQuery("#ups_freight_new_api_username").data('optional', '1');
    jQuery("#ups_freight_new_api_password").data('optional', '1');

    let api_endpoint = jQuery('#ups_freight_api_endpoint').val();
    if(api_endpoint == 'ups_old_api'){
        jQuery('.ups_freight_new_api_field').closest('tr').hide();
        jQuery('.ups_freight_old_api_field').closest('tr').show();

        jQuery("#ups_freight_client_id").data('optional', '1');
        jQuery("#ups_freight_client_secret").data('optional', '1');

        jQuery("#ups_freight_setting_username").removeData('optional');
        jQuery("#ups_freight_setting_password").removeData('optional');
        jQuery("#ups_freight_setting_acccess_key").removeData('optional');

    }else{
        jQuery('.ups_freight_old_api_field').closest('tr').hide();
        jQuery('.ups_freight_new_api_field').closest('tr').show();

        jQuery("#ups_freight_setting_username").data('optional', '1');
        jQuery("#ups_freight_setting_password").data('optional', '1');
        jQuery("#ups_freight_setting_acccess_key").data('optional', '1');

        jQuery("#ups_freight_client_id").removeData('optional');
        jQuery("#ups_freight_client_secret").removeData('optional');
    }

}

if (typeof ups_freight_connection_section_api_endpoint == 'function') {
    ups_freight_connection_section_api_endpoint();
}

if (typeof setNestedMaterialsUI != 'function') {
    function setNestedMaterialsUI() {
        const nestedMaterials = jQuery('._nestedMaterials');
        const productMarkups = jQuery('._en_product_markup');
        
        if (productMarkups?.length) {
            for (const markup of productMarkups) {
                jQuery(markup).attr('maxlength', '7');

                jQuery(markup).keypress(function (e) {
                    if (!String.fromCharCode(e.keyCode).match(/^[0-9.%-]+$/))
                        return false;
                });
            }
        }

        if (nestedMaterials?.length) {
            for (let elem of nestedMaterials) {
                const className = elem.className;

                if (className?.includes('_nestedMaterials')) {
                    const checked = jQuery(elem).prop('checked'),
                        name = jQuery(elem).attr('name'),
                        id = name?.split('_nestedMaterials')[1];
                    setNestMatDisplay(id, checked);
                }
            }
        }
    }
}

if (typeof setNestMatDisplay != 'function') {
    function setNestMatDisplay (id, checked) {
        
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('min', '0');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('max', '100');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('maxlength', '3');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('min', '0');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('max', '100');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('maxlength', '3');

        jQuery(`input[name="_nestedPercentage${id}"], input[name="_maxNestedItems${id}"]`).keypress(function (e) {
            if (!String.fromCharCode(e.keyCode).match(/^[0-9]+$/))
                return false;
        });

        jQuery(`input[name="_nestedPercentage${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedDimension${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`input[name="_maxNestedItems${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedStakingProperty${id}"]`).closest('p').css('display', checked ? '' : 'none');
    }
}