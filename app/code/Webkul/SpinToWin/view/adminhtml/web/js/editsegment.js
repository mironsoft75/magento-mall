define([
    "jquery",
    'mage/translate',
    'Magento_Ui/js/modal/alert',
], function ($, $t, alert) {
    'use strict';
    $.widget('mage.spineditsegmentwidget', {
        options: {
        },
        _create: function () {
            var self = this;

            $('body').on('spinSegmentEditLoaded', function() {
                $('a#spin_tabs_addsegment').attr('href',self.options.edit);
            });
            $('body').on('spinSegmentIndexLoaded', function() {
                $('a#spin_tabs_segments').attr('href',self.options.get);
            });
            $('body').on('change', '#segmentrule_spin_type', function() {
                if(1 == parseInt($(this).val())) { //coupon
                    // $('#segmentrule_value').closest('.admin__field.field').show();
                    $('#segmentrule_conditions_fieldset').show();
                    $('#segmentrule_action_fieldset').show();
                    // $('#segmentrule_value').addClass('required-entry');
                    $('#segmentrule_simple_action').addClass('required-entry');
                    $('#segmentrule_discount_amount').addClass('required-entry');
                    $('#segmentrule_custom_description').addClass('required-entry');
                    $('#segmentrule_coupon_source').addClass('required-entry');
                    $('#segmentrule_rule_description').addClass('required-entry');
                    $('#segmentrule_stop_rules_processing').addClass('required-entry');
                    $('#segmentrule_to_date').addClass('required-entry');
                    $('#segmentrule_product_fieldset').hide();
                    $('#segmentrule_product_sku').removeClass('required-entry');
                    $('#segmentrule_point_fieldset').hide();
                    $('#segmentrule_point').removeClass('required-entry');
                    $('#segmentrule_cloud_type_fieldset').hide();
                    $('#segmentrule_cloud_type').removeClass('required-entry');
                } else if(2 == parseInt($(this).val())) {//product
                    // $('#segmentrule_value').closest('.admin__field.field').hide();
                    $('#segmentrule_conditions_fieldset').hide();
                    $('#segmentrule_action_fieldset').hide();
                    // $('#segmentrule_value').removeClass('required-entry');
                    $('#segmentrule_simple_action').removeClass('required-entry');
                    $('#segmentrule_discount_amount').removeClass('required-entry');
                    $('#segmentrule_stop_rules_processing').removeClass('required-entry');
                    $('#segmentrule_custom_description').removeClass('required-entry');
                    $('#segmentrule_coupon_source').removeClass('required-entry');
                    $('#segmentrule_rule_description').removeClass('required-entry');
                    $('#segmentrule_to_date').removeClass('required-entry');
                    $('#segmentrule_product_fieldset').show();
                    $('#segmentrule_product_sku').addClass('required-entry');
                    $('#segmentrule_point_fieldset').hide();
                    $('#segmentrule_point').removeClass('required-entry');
                    $('#segmentrule_cloud_type_fieldset').hide();
                    $('#segmentrule_cloud_type').removeClass('required-entry');
                }else if(3 == parseInt($(this).val())){//积分
                    $('#segmentrule_conditions_fieldset').hide();
                    $('#segmentrule_action_fieldset').hide();
                    // $('#segmentrule_value').removeClass('required-entry');
                    $('#segmentrule_simple_action').removeClass('required-entry');
                    $('#segmentrule_discount_amount').removeClass('required-entry');
                    $('#segmentrule_custom_description').removeClass('required-entry');
                    $('#segmentrule_coupon_source').removeClass('required-entry');
                    $('#segmentrule_rule_description').removeClass('required-entry');
                    $('#segmentrule_stop_rules_processing').removeClass('required-entry');
                    $('#segmentrule_to_date').removeClass('required-entry');
                    $('#segmentrule_product_fieldset').hide();
                    $('#segmentrule_product_sku').removeClass('required-entry');
                    $('#segmentrule_point_fieldset').show();
                    $('#segmentrule_point').addClass('required-entry');
                    $('#segmentrule_cloud_type_fieldset').hide();
                    $('#segmentrule_cloud_type').removeClass('required-entry');
                }else {//云服务
                    $('#segmentrule_conditions_fieldset').hide();
                    $('#segmentrule_action_fieldset').hide();
                    // $('#segmentrule_value').removeClass('required-entry');
                    $('#segmentrule_simple_action').removeClass('required-entry');
                    $('#segmentrule_discount_amount').removeClass('required-entry');
                    $('#segmentrule_custom_description').removeClass('required-entry');
                    $('#segmentrule_coupon_source').removeClass('required-entry');
                    $('#segmentrule_rule_description').removeClass('required-entry');
                    $('#segmentrule_stop_rules_processing').removeClass('required-entry');
                    $('#segmentrule_to_date').removeClass('required-entry');
                    $('#segmentrule_product_fieldset').hide();
                    $('#segmentrule_product_sku').removeClass('required-entry');
                    $('#segmentrule_point_fieldset').hide();
                    $('#segmentrule_point').removeClass('required-entry');
                    $('#segmentrule_cloud_type_fieldset').show();
                    $('#segmentrule_cloud_type').addClass('required-entry');
                }
            });
            $('body').on('change', '#segmentrule_type', function() {
                if(parseInt($(this).val())) { 
                    // $('#segmentrule_value').closest('.admin__field.field').show();
                    $('#segmentrule_spin_type').closest('.admin__field.field').show().trigger('change');
                    $('#segmentrule_spin_type').trigger('change');
                } else {
                    // $('#segmentrule_value').closest('.admin__field.field').hide();
                    $('#segmentrule_spin_type').closest('.admin__field.field').hide();
                    $('#segmentrule_conditions_fieldset').hide();
                    $('#segmentrule_action_fieldset').hide();
                    $('#segmentrule_product_fieldset').hide();
                    $('#segmentrule_product_sku').removeClass('required-entry');
                    // $('#segmentrule_value').removeClass('required-entry');
                    $('#segmentrule_simple_action').removeClass('required-entry');
                    $('#segmentrule_discount_amount').removeClass('required-entry');
                    $('#segmentrule_stop_rules_processing').removeClass('required-entry');
                    $('#segmentrule_to_date').removeClass('required-entry');
                    $('#segmentrule_point_fieldset').hide();
                    $('#segmentrule_point').removeClass('required-entry');
                    $('#segmentrule_cloud_type_fieldset').hide();
                    $('#segmentrule_cloud_type').removeClass('required-entry');
                }
            });
            $('body').on('click', '#update-spineditsegmentform', function() {
                if (!validateSegment()) {
                    // var formData = $('body').find('div.ui-tabs-panel[aria-labelledby="spin_tabs_addsegment"]').find('select, textarea, input').serialize();
                    // var formData = $('body').find('div.ui-tabs-panel[aria-labelledby="spin_tabs_addsegment"]').find('select, textarea, input').serialize();
                    const formData = new FormData();
                    $('body').find('div.ui-tabs-panel[aria-labelledby="spin_tabs_addsegment"]').find('select, textarea, input').each(function(i, ele) {
                        const $ele = $(ele);
                        if ($ele.is('#segmentrule_image')) {
                            const file = $ele[0].files[0];
                            if(file){
                                 formData.append($ele.attr('name'), file, file.name);
                            }
                           
                        } else {
                            formData.append($ele.attr('name'), $ele.val());
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: self.options.save,
                        data: formData,
                        // data: new FormData($('#edit_form')[0]),
                        // dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('body').trigger('processStart');
                        },
                        success: function(response)
                        {
                            if (parseInt(response.success)) {
                                $(self).find('.admin__page-nav-item-message').addClass('_changed');
                                $('body').find('a#spin_tabs_segments').trigger('click');
                                alert({
                                    title: $t('Success'),
                                    content: $t(response.message)
                                });
                                if (response.data !== undefined && response.data) {
                                    $(form).trigger("spinAjaxComplete", [response.data]);
                                }
                            } else {
                                alert({
                                    title: $t('Error'),
                                    content: $t(response.message)
                                });
                            }
                            $('body').trigger('processStop');
                        },
                        error: function (response) {
                            $('body').trigger('processStop');
                        }
                    });
                }
            });
            function validateSegment() {
                var errorflag = false;
                var toValidate = [];
                $.each(toValidate, function (ind, value) { 
                    if(!$.validator.validateElement(value)) {
                        errorflag = true;
                        $(value).addClass('mage-error');
                    } else {
                        $(value).removeClass('mage-error');
                    }
                });

                return errorflag;
            }
            $('body').on('click', '.spin-segment-edit-action', function (e) {
                var segmentlink = $('a#spin_tabs_addsegment').attr('href');
                segmentlink += 'id/'+$(this).data('id');
                $('a#spin_tabs_addsegment').attr('href', segmentlink);
                $('a#spin_tabs_addsegment').trigger('click');
            });
        }
    });
    return $.mage.spineditsegmentwidget;
});
