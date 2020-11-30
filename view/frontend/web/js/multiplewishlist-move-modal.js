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
            modalOptions: {
                title: $t('Move Items to Wishlist'),
                responsive: true,
                form: '#multiple-wishlist-move-form',
                trigger: '[data-role=move]',
                buttons: [
                    {
                        text: $t('Close')
                    },
                    {
                        text: $t('Move Items'),
                        class: 'action primary',
                        click: function () {
                            $(this.options.form).submit();
                        }
                    }
                ]
            },
        },

        _create: function() {
            let widget = this;

            $(document).on('click', widget.options.modalOptions.trigger, function (e) {
                e.preventDefault();

                let moveForm = $(widget.element).find('form'),
                    item = $(this).data('item');

                moveForm.find('input[name=item_id]').remove();

                if (item) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'item_id',
                        value: item,
                    }).appendTo(moveForm);
                }
            })

            modal(this.options.modalOptions, $(this.element));
        },
    })

    return $.bkozlic.multiplewishlistmodal;
});
