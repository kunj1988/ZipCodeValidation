/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.region_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var options = this.initialOptions,
                isRegionRequired,
                option;

            if (!value) {
                return;
            }
            option = options[value];

            if (typeof option === 'undefined') {
                return;
            }
            this.setOptions(option);
        },

        /**
         * Filters 'initialOptions' property by 'field' and 'value' passed,
         * calls 'setOptions' passing the result to it
         *
         * @param {*} value
         * @param {String} field
         */
        filter: function (value, field) {
            var result;
            if (!field) { //validate field, if we are on update
                field = this.filterBy.field;
            }
            this._super(value, field);

            if(this.initialOptions[value]) {
                result = this.initialOptions[value];
            }else{
                result = false;
            }
            this.setOptions(result);
        }
    });
});

