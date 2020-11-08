/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'underscore',
    'ko',
    'mage/translate'
], function (Component, customerData, $, _, ko, $t) {
    'use strict';

    return Component.extend({
        wishlistNameValue: ko.observable(''),
        ajaxProcess: ko.observable(false),

        /**
         * Checks if there are multiple wishlists
         * @returns {boolean}
         */
        hasWishlists: function () {
            let wishlist = customerData.get('multiple-wishlist')();

            return !!(wishlist.items && _.size(wishlist.items));
        },

        /**
         * Returns wishlists
         * @returns {Object}
         */
        getMultipleWishlists: function () {
            return customerData.get('multiple-wishlist')().items;
        },

        /**
         * Creates new wishlist
         */
        createNew: function () {
            let createUrl = customerData.get('multiple-wishlist')().createUrl,
                component = this,
                wishlistName = this.wishlistNameValue();

            if (createUrl && !this.ajaxProcess()) {
                //@TODO Show message if error
                this.ajaxProcess(true);
                $.post({
                    url: createUrl,
                    data: {
                        name: wishlistName
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    complete: function () {
                        component.wishlistNameValue('');
                        component.ajaxProcess(false);
                    }
                });
            }
        }
    });
});