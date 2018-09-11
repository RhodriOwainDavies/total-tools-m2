define(
    [
        'jquery',
        'uiComponent'
    ],
    function ($, Component) {
        'use strict';
        var arr = $.map(window.ShippingAdditionalMessage, function (el) {
            return el;
        });
        return Component.extend({
            defaults: {
                template: 'Temando_Temando/checkout/shipping/additional-block'
            },
            shippingAdditionalMessage: arr
        });
    }
);