/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator'
], function ($, _, registry, Abstract, validator) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.city:value'
            }
        },
        initialize: function () {
            var self = this;
            validator.addRule(
                "custom-zip-code-validate",
                function (value) {
                    return self._validatePostCode(value);
                },
                $.mage.__("Please enter a valid zip code.")
            );
            this.validation = _.omit(this.validation, 'custom-zip-code-validate');
            this.updateZipCodeValidation(true);
            this._super();
        },
        /**
         * Defines if value has changed.
         *
         * @returns {Boolean}
         */
        hasChanged: function () {
            var customZipCodeValidate = false;
            if(!this._validatePostCode(this.value())) {
                this.validate();
                customZipCodeValidate = true;
            }
            this.updateZipCodeValidation(customZipCodeValidate);
            this._super();
        },
        /**
         * @param {Boolean} status
         */
        updateZipCodeValidation: function(status) {
            this.validation['custom-zip-code-validate'] = status;
        },
        /**
         * @param {String} value
         */
        _validatePostCode: function(value) {
            var city = registry.get(this.parentName + '.' + 'city'),
                options = city.indexedOptions,
                option = null;

            if (!value) {
                return;
            }

            option = options[city.value()];
            if (!option) {
                return;
            }
            if(option['zip_code']) {
                return $.inArray(value, option['zip_code']) >= 0 ? 1:0;
            }
        },
        /**
         * @param {String} value
         */
        update: function (value) {
            var city = registry.get(this.parentName + '.' + 'city'),
                options = city.indexedOptions,
                option = null,
                customZipCodeValidate = false;


            if (!value) {
                return;
            }

            option = options[value];

            if (!option) {
                return;
            }
            if(!this._validatePostCode(this.value())) {
                this.reset();
                var newValue = this.value();
                customZipCodeValidate = true;
            }
            this.updateZipCodeValidation(customZipCodeValidate);
        }
    });
});
