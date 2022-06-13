<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-paylike" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                        class="btn btn-default"><i class="fa fa-reply"></i></a>
                <a href="<?php echo $paylike_payments; ?>" data-toggle="tooltip"
                        title="<?php echo $button_paylike_payments; ?>" class="btn btn-success"><i
                            class="fa fa-calculator"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if($error_warning){ ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if($success){ ?>
        <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title" data-paylike-version="<?php echo $text_paylike_version; ?>"><i class="fa fa-pencil"></i>&nbsp;<?php echo $text_edit_settings; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-paylike"
                        class="form-horizontal">

                    <div class="alert alert-info"><?php echo $text_description; ?></div>


                    {* Store select begin *}
                    <div class="col-sm-12 pr-40 required">
                        <label class="col-sm-2 control-label" for="payment_paylike_selected_store"><span
                                    data-toggle="tooltip"
                                    title="<?php echo $help_select_store; ?>"><?php echo $select_store; ?></span></label>
                        <div class="col-sm-10">
                            <select name="payment_paylike_selected_store" id="payment_paylike_selected_store"
                                    class="form-control">
                                <?php foreach ($stores as $store): ?>
                                        <option value="<?php echo $store['store_id']; ?>"
                                            <?php if($store['store_id'] == $payment_paylike_selected_store){
                                                echo 'selected="selected"';
                                            } ?>
                                            >
                                            <?php echo $store['name']; ?>
                                        </option>
                                <?php endforeach ?>
                            </select>
                            <div class="select-store-dropdown-error"></div>
                        </div>
                    </div>
                    {* Store select end *}


                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-general_settings"
                                    data-toggle="tab"><?php echo $text_general_settings; ?></a></li>
                        <li><a href="#tab-advanced_settings"
                                    data-toggle="tab"><?php echo $text_advanced_settings; ?></a></li>
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane active" id="tab-general_settings">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_paylike_status"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_payment_enabled; ?>"><?php echo $entry_payment_enabled; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_status" id="input_paylike_status"
                                            class="form-control">
                                    <?php if($payment_paylike_status){ ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input_payment_method_title"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_payment_method_title; ?>"><?php echo $entry_payment_method_title; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_method_title"
                                            value="<?php echo $payment_paylike_method_title; ?>"
                                            placeholder="<?php echo $entry_payment_method_title; ?>"
                                            id="input_payment_method_title" class="form-control"/>
                                    <?php if($error_payment_method_title){ ?>
                                    <div class="text-danger"><?php echo $error_payment_method_title; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span data-toggle="tooltip"
                                            title="<?php echo $help_checkout_cc_logo; ?>"><?php echo $entry_checkout_cc_logo; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="well well-sm" style="overflow: auto;">
                                        <?php foreach($ccLogos as $ccLogo){ ?>
                                        <div class="checkbox">
                                            <label>
                                                <?php if(is_array($payment_paylike_checkout_cc_logo) && in_array($ccLogo['logo'], $payment_paylike_checkout_cc_logo)){ ?>
                                                <input type="checkbox" name="payment_paylike_checkout_cc_logo[]"
                                                        value="<?php echo $ccLogo['logo']; ?>" checked="checked"/>
                                                <?php echo $ccLogo['name']; ?>
                                                <?php } else { ?>
                                                <input type="checkbox" name="payment_paylike_checkout_cc_logo[]"
                                                        value="<?php echo $ccLogo['logo']; ?>"/>
                                                <?php echo $ccLogo['name']; ?>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_checkout_popup_title"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_checkout_popup_title; ?>"><?php echo $entry_checkout_popup_title; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_checkout_title"
                                            value="<?php echo $payment_paylike_checkout_title; ?>"
                                            placeholder="<?php echo $entry_checkout_popup_title; ?>"
                                            id="input_checkout_popup_title" class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_api_mode"><span data-toggle="tooltip"
                                            title="<?php echo $help_api_mode; ?>"><?php echo $entry_api_mode; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_api_mode" id="input_api_mode" class="form-control">
                                        <?php if($payment_paylike_api_mode == 'test'){ ?>
                                        <option value="test" selected="selected"><?php echo $text_test; ?></option>
                                        <option value="live"><?php echo $text_live; ?></option>
                                        <?php } else { ?>
                                        <option value="test"><?php echo $text_test; ?></option>
                                        <option value="live" selected="selected"><?php echo $text_live; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group required js-test-key">
                                <label class="col-sm-2 control-label" for="input_app_key_test"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_app_key_test; ?>"><?php echo $entry_app_key_test; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_app_key_test"
                                            value="<?php echo $payment_paylike_app_key_test; ?>"
                                            placeholder="<?php echo $entry_app_key_test; ?>" id="input_app_key_test"
                                            class="form-control"/>
                                    <?php if($error_app_key_test){ ?>
                                    <div class="text-danger"><?php echo $error_app_key_test; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group required js-test-key">
                                <label class="col-sm-2 control-label" for="input_public_key_test"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_public_key_test; ?>"><?php echo $entry_public_key_test; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_public_key_test"
                                            value="<?php echo $payment_paylike_public_key_test; ?>"
                                            placeholder="<?php echo $entry_public_key_test; ?>"
                                            id="input_public_key_test" class="form-control"/>
                                    <?php if($error_public_key_test){ ?>
                                    <div class="text-danger"><?php echo $error_public_key_test; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group required js-live-key">
                                <label class="col-sm-2 control-label" for="input_app_key_live"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_app_key_live; ?>"><?php echo $entry_app_key_live; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_app_key_live"
                                            value="<?php echo $payment_paylike_app_key_live; ?>"
                                            placeholder="<?php echo $entry_app_key_live; ?>" id="input_app_key_live"
                                            class="form-control"/>
                                    <?php if($error_app_key_live){ ?>
                                    <div class="text-danger"><?php echo $error_app_key_live; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group required js-live-key">
                                <label class="col-sm-2 control-label" for="input_public_key_live"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_public_key_live; ?>"><?php echo $entry_public_key_live; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_public_key_live"
                                            value="<?php echo $payment_paylike_public_key_live; ?>"
                                            placeholder="<?php echo $entry_public_key_live; ?>"
                                            id="input_public_key_live" class="form-control"/>
                                    <?php if($error_public_key_live){ ?>
                                    <div class="text-danger"><?php echo $error_public_key_live; ?></div>
                                    <?php } ?>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_capture_mode"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_capture_mode; ?>"><?php echo $entry_capture_mode; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_capture_mode" id="input_capture_mode"
                                            class="form-control">
                                        <?php if($payment_paylike_capture_mode == 'instant'){ ?>
                                        <option value="instant"
                                                selected="selected"><?php echo $text_capture_instant; ?></option>
                                        <option value="delayed"><?php echo $text_capture_delayed; ?></option>
                                        <?php } else { ?>
                                        <option value="instant"><?php echo $text_capture_instant; ?></option>
                                        <option value="delayed"
                                                selected="selected"><?php echo $text_capture_delayed; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>


                        </div>


                        <div class="tab-pane" id="tab-advanced_settings">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_authorize_status_id"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_authorize_status_id; ?>"><?php echo $entry_authorize_status_id; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_authorize_status_id" id="input_authorize_status_id"
                                            class="form-control">
                                        <?php foreach($order_statuses as $order_status){ ?>
                                        <?php if($order_status['order_status_id'] == $payment_paylike_authorize_status_id){ ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_capture_status_id"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_capture_status_id; ?>"><?php echo $entry_capture_status_id; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_capture_status_id" id="input_capture_status_id"
                                            class="form-control">
                                        <?php foreach($order_statuses as $order_status){ ?>
                                        <?php if($order_status['order_status_id'] == $payment_paylike_capture_status_id){ ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_refund_status_id"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_refund_status_id; ?>"><?php echo $entry_refund_status_id; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_refund_status_id" id="input_refund_status_id"
                                            class="form-control">
                                        <?php foreach($order_statuses as $order_status){ ?>
                                        <?php if($order_status['order_status_id'] == $payment_paylike_refund_status_id){ ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_void_status_id"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_void_status_id; ?>"><?php echo $entry_void_status_id; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_void_status_id" id="input_void_status_id"
                                            class="form-control">
                                        <?php foreach($order_statuses as $order_status){ ?>
                                        <?php if($order_status['order_status_id'] == $payment_paylike_void_status_id){ ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_logging"><span data-toggle="tooltip"
                                            title="<?php echo $help_logging; ?>"><?php echo $entry_logging; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_logging" id="input_logging" class="form-control">
                                        <?php if($payment_paylike_logging){ ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_minimum_total"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_minimum_total; ?>"><?php echo $entry_minimum_total; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_minimum_total"
                                            value="<?php echo $payment_paylike_minimum_total; ?>"
                                            placeholder="<?php echo $entry_minimum_total; ?>" id="input_minimum_total"
                                            class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_geo_zone"><span data-toggle="tooltip"
                                            title="<?php echo $help_geo_zone; ?>"><?php echo $entry_geo_zone; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="payment_paylike_geo_zone" id="input_geo_zone" class="form-control">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach($geo_zones as $geo_zone){ ?>
                                        <?php if($geo_zone['geo_zone_id'] == $payment_paylike_geo_zone){ ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                                selected="selected"><?php echo $geo_zone['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input_paylike_sort_order"><span
                                            data-toggle="tooltip"
                                            title="<?php echo $help_sort_order; ?>"><?php echo $entry_sort_order; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_paylike_sort_order"
                                            value="<?php echo $payment_paylike_sort_order; ?>"
                                            placeholder="<?php echo $entry_sort_order; ?>" id="input_paylike_sort_order"
                                            class="form-control"/>
                                </div>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#tabs a:first').tab('show');

    /** Triggern on selection change */
    $(document).on('change','#input_api_mode', function(){
        toggleFields($(this).val());
    });
    /** Triggern on when DOM is ready */
    toggleFields($("#input_api_mode").val());

    /** Show/Hide live and test fields based on selected value of the "#input_api_mode" selectbox*/
    function toggleFields(mode){
        if(mode == 'live'){
            $('.js-live-key').css('display','block');
            $('.js-test-key').css('display','none');
        } else {
            $('.js-live-key').css('display','none');
            $('.js-test-key').css('display','block');
        }
    }


    /**
     * On select a store, populate paylike form data with seleted store Paylike data
     */
    $("#payment_paylike_selected_store").on("change", function() {

        /** Remove any previous errors. */
        $(".select-store-dropdown-error").val();

        /** Set store ID variable. */
        var storeId = $(this).val();

        /** Ajax call to get selected store paylike data. */
        $.ajax({
            method: "POST",
            url: "index.php?route=extension/payment/paylike/get_paylike_store_settings&user_token=" + getURLVar("user_token"),
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            data: {
                store_id: storeId
            },
            success: function (data, status, xhr) {

                /** Add selected attribute to selected store option */
                setSelectedAttributeOnSelectedOption(this.id, storeId)

                /** Add status option to status input. If not exists, change to disabled. */
                $("#input_paylike_status option").each(function () {
                    if (data?.["payment_paylike_status"]) {
                        if (data["payment_paylike_status"] !== $(this).val()) {
                            $(this).removeAttr("selected");
                        } else {
                            $(this).attr("selected", true);
                        }
                    } else {
                        if (1 == $(this).val()) {
                            $(this).removeAttr("selected");
                        } else {
                            $(this).attr("selected", true);
                        }
                    }
                });

                $("#input_payment_method_title").val(data?.["payment_paylike_method_title"]);
                $("#input_checkout_popup_title").val(data?.["payment_paylike_checkout_title"]);

                $("input[name='payment_paylike_checkout_cc_logo[]']").each(function () {
                    if ("payment_paylike_checkout_cc_logo" in data) {
                        if ( ! data["payment_paylike_checkout_cc_logo"].includes($(this).val())) {
                            $(this).prop("checked", false);
                        } else {
                            $(this).prop("checked", true);
                        }
                    } else {
                        $(this).prop("checked", true);
                    }
                });


                /** Set selected on api mode dropdown. Default = live. */
                setSelectedAttributeOnSelectedOption("input_api_mode", data?.["payment_paylike_api_mode"] ?? "live");
                toggleFields(data?.["payment_paylike_api_mode"] ?? "live")
                /** Set selected on capture mode dropdown. Default = delayed. */
                setSelectedAttributeOnSelectedOption("input_capture_mode", data?.["payment_paylike_capture_mode"] ?? "delayed");

                $("#input_app_key_test").val(data?.["payment_paylike_app_key_test"]);
                $("#input_public_key_test").val(data?.["payment_paylike_public_key_test"]);
                $("#input_app_key_live").val(data?.["payment_paylike_app_key_live"]);
                $("#input_public_key_live").val(data?.["payment_paylike_public_key_live"]);
                /**
                 * !!! HARDCODED ORDER STATUSES IDS !!!
                 */
                $("#input_authorize_status_id").val(data?.["payment_paylike_authorize_status_id"] ?? 1);
                $("#input_capture_status_id").val(data?.["payment_paylike_capture_status_id"] ?? 5);
                $("#input_refund_status_id").val(data?.["payment_paylike_refund_status_id"] ?? 11);
                $("#input_void_status_id").val(data?.["payment_paylike_void_status_id"] ?? 16);

                $("#input_logging").val(data?.["payment_paylike_logging"] ?? 0);
                $("#input_minimum_total").val(data?.["payment_paylike_minimum_total"] ?? 0);
                $("#input_geo_zone").val(data?.["payment_paylike_geo_zone"] ?? 0);
                $("#input_sort_order").val(data?.["payment_paylike_sort_order"] ?? 0);

            },
            error: function (jqXhr, textStatus, errorMessage) {
                /** Check if error is of type "parsererror". This shows up when token expire. */
                if ('parsererror' == textStatus) {
                    location.href = 'index.php?route=extension/payment/paylike';
                } else {
                    $(".select-store-dropdown-error").append("Error: " + errorMessage).addClass("text-danger");
                    console.error(errorMessage)
                }
            }
        });

    });


    /** Paylike status set "selected" on change. */
    $("#input_paylike_status").on("change", function() {
        setSelectedAttributeOnSelectedOption(this.id, $(this).val());
    });

    /** Paylike api mode set "selected" on change. */
    $("#input_api_mode").on("change", function() {
        setSelectedAttributeOnSelectedOption(this.id, $(this).val());
    });

    /**
     * Function "Set Selected Attribute On Selected Option"
     * Set selected attribute on dropdown selected option
     */
    function setSelectedAttributeOnSelectedOption(selector, value) {
        $("#" + selector + " option").each(function () {
            if (value !== $(this).val()) {
                $(this).removeAttr("selected");
            } else {
                $(this).attr("selected", true);
            }
        });
    }

</script>

<?php echo $footer; ?>
