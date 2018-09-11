require(
    [
        'jquery',
        'jquery/ui'
    ],
    function ($) {
        var ruleType = $("#rule_action_rate_type");
        showHideActionFields(ruleType.val());

        var adjustmentType = $("#rule_action_dynamic_adjustment_type");
        showHideRateAdjustmentFields(adjustmentType.val(), 0);

        $("#rule_action_rate_type").change(function (e) {
            showHideActionFields(e.currentTarget.value);
        });

        $("#rule_action_dynamic_adjustment_type").change(function (e) {
            showHideRateAdjustmentFields(e.currentTarget.value, 1);
        });

        function showHideActionFields(value)
        {
            switch (value) {
                case '1': //flat rate
                    $("#rule_static_rate_fieldset-wrapper").show();
                    $("#rule_dynamic_rate_fieldset-wrapper").hide();
                    $("#rule_action_static_value").removeAttr('disabled');
                    break;
                case '2': //free shipping
                    $("#rule_static_rate_fieldset-wrapper").show();
                    $("#rule_dynamic_rate_fieldset-wrapper").hide();
                    $("#rule_action_static_value").val(0).attr('disabled', 'disabled');
                    break;
                case '3': //dynamic
                    $("#rule_dynamic_rate_fieldset-wrapper").show();
                    $("#rule_static_rate_fieldset-wrapper").hide();
                    break;
            }
        }

        function showHideRateAdjustmentFields(value, resetValues)
        {
            switch (value) {
                case '1': //no adjustment
                    $(".field-action_dynamic_adjustment_value").hide();
                    $(".field-action_dynamic_adjustment_roundup").hide();
                    if (resetValues) {
                        $("#rule_action_dynamic_adjustment_roundup").val(0);
                        $("#rule_action_dynamic_adjustment_value").val('');
                    }
                    //$("#rule_action_static_value").removeAttr('disabled');
                    break;
                case '2': //override
                    if (resetValues) {
                        $("#rule_action_dynamic_adjustment_value").val('');
                        $("#rule_action_dynamic_adjustment_roundup").val(0);
                    }
                    $(".field-action_dynamic_adjustment_value").show();
                    $(".field-action_dynamic_adjustment_roundup").show();
                    break;
                case '3': //markup
                    if (resetValues) {
                        $("#rule_action_dynamic_adjustment_value").val('');
                        $("#rule_action_dynamic_adjustment_roundup").val(0);
                    }
                    $(".field-action_dynamic_adjustment_value").show();
                    $(".field-action_dynamic_adjustment_roundup").show();
                    break;
            }
        }
    }
);