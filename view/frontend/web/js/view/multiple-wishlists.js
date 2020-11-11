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
        createError: ko.observable(false),
        createNewText: $t('Create New Wishlist'),
        creatingText: $t('Creating'),
        createButtonText: ko.observable(),

        initialize: function () {
            this._super();
            this.createButtonText(this.createNewText);
        },

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

            if (!wishlistName.trim()) {
                this.createError(true);
            }

            if (createUrl && !this.ajaxProcess() && wishlistName.trim()) {
                this.ajaxProcess(true);
                //@TODO Check translations
                this.createButtonText(this.creatingText);
                $.post({
                    url: createUrl,
                    data: {
                        name: wishlistName
                    },
                    success: function (data) {
                        //@TODO Process success/error
                    },
                    complete: function () {
                        component.wishlistNameValue('');
                        component.ajaxProcess(false);
                        component.createError(false);
                        component.createButtonText(component.createNewText);
                    }
                });
            }
        }
    });
});
