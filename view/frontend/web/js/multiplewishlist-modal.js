/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function($, modal, $t) {
    'use strict';

    $.widget('bkozlic.modal', {
        options: {
            modalOptions: {
                title: $t('Add to wishlist'),
                responsive: true,
                trigger: '[data-action=add-to-wishlist]',
                buttons: [
                    {
                        text: $t('Close')
                    },
                    {
                        text: $t('Add Item To Selected Wishlist'),
                    }
                ]
            },
        },

        _create: function() {
            this._prepareElements();
            modal(this.options.modalOptions, $(this.element));
        },

        /**
         * Remove default data attribute to prevent default request
         * @private
         */
        _prepareElements: function () {
            $(this.options.modalOptions.trigger).attr('data-multiple', function () {
                return JSON.stringify($(this).data('post'));
            });

            $(this.options.modalOptions.trigger).on('click', function (e) {
                e.preventDefault();
            })

            $(this.options.modalOptions.trigger).removeAttr('data-post');
        }
    })

    return $.bkozlic.modal
});
