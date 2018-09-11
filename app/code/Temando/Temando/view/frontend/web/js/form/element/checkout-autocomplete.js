/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'domReady!'
], function (_, registry, Abstract, ko, $, quote, rateRegistry) {
    'use strict';
    var xhr;
    var baseurl;
    var targetValue;
    var otherValue;
    var chosenCity;
    var chosenPostcode;
    return Abstract.extend({

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            _.bindAll(this, 'reset');

            this.on('keyup', this.onUpdate);

            this._super()
                .setInitialValue()
                ._setClasses()
                .initSwitcher();

            return this;
        },
        onUpdate: function ( ) {
            baseurl = $('#baseurl').val();
            $("#autocomplete-suggestion").remove();

            var country = registry.get(this.parentName + '.' + 'country_id').value();
            var targetElement;
            var otherElement;
            var top;
            var left;
            var position;

            if ($('div#opc-new-shipping-address').css('display') == "block") {
                if (this.name == 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city') {
                    targetElement = $('div#opc-new-shipping-address input[name="city"]');
                    otherElement = $('div#opc-new-shipping-address input[name="postcode"]');
                    top = 417;
                } else if (this.name == 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode') {
                    targetElement = $('div#opc-new-shipping-address input[name="postcode"]');
                    otherElement = $('div#opc-new-shipping-address input[name="city"]');
                    top = 575;
                }
                left = 0;
                position = 'relative';
                if (targetElement == "undefined") {
                    return false;
                }
            } else {
                if (this.name == 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city') {
                    targetElement = $('input[name="city"]');
                    otherElement = $('input[name="postcode"]');
                } else if (this.name == 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode') {
                    targetElement = $('input[name="postcode"]');
                    otherElement = $('input[name="city"]');
                }
                if (typeof targetElement.offset() === "undefined") {
                    return false;
                }
                top = targetElement.offset().top + 35;
                left = targetElement.offset().left;
            }
            targetValue = targetElement.val();
            otherValue = otherElement.val();

            if ((targetValue == chosenCity) && (otherValue == chosenPostcode)) {
                return false;
            }

            if ((targetValue == chosenPostcode) && (otherValue == chosenCity)) {
                return false;
            }

            var regex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
            var query = targetElement.val();//$("#" + e.target.getAttribute('id')).val();
            var locations;
            var suggestionDiv;
            //if (regex.test(query) && e.keyCode != 17) {
            if (regex.test(query)) {
                otherElement.removeClass('avs-active');
                targetElement.addClass('avs-active');
                var param = 'query=' + query + '&country='+country;

                if (xhr && xhr.readystate != 4) {
                    xhr.abort();
                }
                xhr = $.ajax({
                    url: baseurl + 'temando/api/pcs',
                    data: param,
                    type: 'post',
                    dataType: 'json'
                }).done(function (data) {
                    $("#autocomplete-suggestion").remove();
                    locations = data['suggestions'];
                    if (locations.length > 0) {
                        suggestionDiv = '';
                        suggestionDiv = "<div id='autocomplete-suggestion' style='";
                        suggestionDiv += "top:" + top + "px; "
                        suggestionDiv += "left:" + left + "px;"
                        if (typeof postition === "undefined") {
                        } else {
                            suggestionDiv += "position:" + position;
                        }
                        suggestionDiv += "'>";
                        $.each(locations, function (query, location) {
                            var splitLocation = location.split(',', 2);
                            var suburb = jQuery.trim(splitLocation[0]);
                            var postcodeCountry = jQuery.trim(splitLocation[1]);
                            var countryIndex = postcodeCountry.lastIndexOf(" ");
                            var postcode = postcodeCountry.substring(0, countryIndex);

                            suggestionDiv += "<div class='location' postcode='" + postcode +
                                "' suburb='" + suburb + "'>" + location + "</div>";
                        });
                        suggestionDiv += "</div>";

                        $("#co-shipping-form").append(suggestionDiv);
                        targetElement.removeClass('avs-active');

                        $('.location').on('click', function (e) {
                            chosenCity = e.target.getAttribute('suburb');
                            $("input[name='city']").val(e.target.getAttribute('suburb'));

                            chosenPostcode = e.target.getAttribute('postcode');
                            $("input[name='postcode']").val(e.target.getAttribute('postcode'));

                            $("input[name='city']").keyup();
                            $("input[name='postcode']").keyup();

                            $("#autocomplete-suggestion").remove();

                            var shippingAddress = quote.shippingAddress();
                            shippingAddress.city = e.target.getAttribute('suburb');
                            shippingAddress.postcode = e.target.getAttribute('postcode');
                            shippingAddress.trigger_reload = new Date().getTime();

                            rateRegistry.set(shippingAddress.getKey(), null);
                            rateRegistry.set(shippingAddress.getCacheKey(), null);

                            quote.shippingAddress(shippingAddress);

                            var xhrAdditional = $.ajax({
                                url: baseurl + 'temando/checkout/getadditionalmessage',
                                data: {shippingAddress:JSON.stringify(shippingAddress)},
                                type: 'post',
                                dataType: 'json'
                            }).done(function (data) {
                                $.each(data, function (messageType, messageArr) {
                                    if (messageArr['message'].length) {
                                        var additionalMessage = '';
                                        $.each(messageArr['message'], function (i, msg) {
                                            additionalMessage += '<div>'+msg+'</div>';
                                        });
                                        $('.shipping-additional-message.'+messageType).html(additionalMessage);
                                        $('.shipping-additional-message.'+messageType).show();
                                    } else {
                                        $('.shipping-additional-message.'+messageType).html('');
                                        $('.shipping-additional-message.'+messageType).hide();
                                    }
                                });
                            });
                        });
                    }
                });
            }
        },

        /**
         * Update.
         *
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            option = options[value];
            if (option['is_zipcode_optional']) {
                this.error(false);
                this.validation = _.omit(this.validation, 'required-entry');
            } else {
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
        }
    });
});