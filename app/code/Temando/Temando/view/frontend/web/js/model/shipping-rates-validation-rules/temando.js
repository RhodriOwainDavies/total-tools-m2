/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function () {
                return {
                    'postcode': {
                        'required': true
                    },
                    'country_id': {
                        'required': true
                    },
                    'city': {
                        'required': true
                    },
                    'authority_to_leave':
                    {
                        'required': false
                    },
                    'is_business_address':
                    {
                        'required': false
                    }
                };
            }
        };
    }
);
