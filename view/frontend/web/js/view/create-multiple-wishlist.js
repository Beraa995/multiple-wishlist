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
    'ko',
    'mage/translate',
    'mage/cookies'
], function (Component, customerData, $, ko, $t) {
    'use strict';

    return Component.extend({
        wishlistNameValue: ko.observable(''),
        ajaxProcess: ko.observable(false),
        createError: ko.observable(false),
        createNewText: $t('Create New Wishlist'),
        creatingText: $t('Creating'),
        requiredFieldText: $t('This is a required field.'),
        createButtonText: ko.observable(),
        errorText: ko.observable(),

        initialize: function () {
            this._super();
            this.createButtonText(this.createNewText);
            this.errorText(this.requiredFieldText);
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
                this.errorText(this.requiredFieldText);
            }

            if (createUrl && !this.ajaxProcess() && wishlistName.trim()) {
                this.ajaxProcess(true);
                this.createButtonText(this.creatingText);
                $.post({
                    url: createUrl,
                    data: {
                        name: wishlistName,
                        form_key: $.mage.cookies.get('form_key'),
                    },
                    success: function (data) {
                        if (!data.success) {
                            component.createError(true);
                            component.errorText(data.message);
                        } else {
                            component.createError(false);
                            component.wishlistNameValue('');
                        }
                    },
                    complete: function () {
                        component.ajaxProcess(false);
                        component.createButtonText(component.createNewText);
                    }
                });
            }
        }
    });
});
