/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/template',
    'text!BKozlic_MultipleWishlist/templates/hidden-inputs.html',
], function($, modal, $t, mageTemplate, inputsTemplate) {
    'use strict';

    $.widget('bkozlic.multiplewishlistmodal', {
        options: {
            hiddenInputs: '.multiple-wishlist-hidden',
            modalOptions: {
                title: $t('Add to wishlist'),
                responsive: true,
                form: '#multiple-wishlist-form',
                trigger: '[data-action=add-to-wishlist]',
                buttons: [
                    {
                        text: $t('Close')
                    },
                    {
                        text: $t('Add Item To Selected Wishlist'),
                        class: 'action primary',
                        click: function () {
                            $(this.options.form).submit();
                        }
                    }
                ]
            },
        },

        _create: function() {
            this._prepareElements();
            modal(this.options.modalOptions, $(this.element));
        },

        /**
         * Remove default data attribute and create new from in the modal
         * @private
         */
        _prepareElements: function () {
            let widget = this;
            //@TODO If there are items loaded by ajax this won't work. Prevent click globally.
            $(widget.options.modalOptions.trigger).attr('data-multiple', function () {
                return JSON.stringify($(this).data('post'));
            });

            $(document).on('click', widget.options.modalOptions.trigger, function (e) {
                e.preventDefault();

                let postData = $(this).data('multiple'),
                    inputsHidden = $(mageTemplate(inputsTemplate, {
                        data: postData
                    })),
                    multipleWishlistForm = $(widget.element).find('form');

                multipleWishlistForm.attr('action', postData.action);
                multipleWishlistForm.find(widget.options.hiddenInputs).remove()
                multipleWishlistForm.append(inputsHidden)
            })

            $(widget.options.modalOptions.trigger).removeAttr('data-post');
        },
    })

    return $.bkozlic.multiplewishlistmodal;
});
