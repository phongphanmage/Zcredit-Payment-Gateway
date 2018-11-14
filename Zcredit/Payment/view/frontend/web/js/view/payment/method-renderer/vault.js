/*browser:true*/
/*global define*/
define(
    [
        'Magento_Vault/js/view/payment/method-renderer/vault',
        'mage/translate',
        'jquery'
    ],
    function (Component, $t, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Zcredit_Payment/payment/vault-form'
            },

            /**
             * Get last 4 digits of card
             * @returns {String}
             */
            getMaskedCard: function () {
                return this.details.maskedCC;
            },
    
            /**
             * Get expiration date
             * @returns {String}
             */
            getExpirationDate: function () {
                return this.details.expirationDate;
            },
    
            /**
             * Get card type
             * @returns {String}
             */
            getCardType: function () {
                return this.details.type;
            },
    
            /**
             * Get payment method token
             * @returns {String}
             */
            getToken: function () {
                return this.publicHash;
            },

            /**
             * @returns {Boolean}
             */
            isValidateCvv: function () {
                return typeof window.checkoutConfig.payment.zcredit_cc.validate_cvv !== 'undefined' &&
                    window.checkoutConfig.payment.zcredit_cc.validate_cvv == 1;
            },

            /**
             * Get image url for CVV
             * @returns {String}
             */
            getCvvImageUrl: function () {
                return window.checkoutConfig.payment.ccform.cvvImageUrl['zcredit_cc'];
            },

            /**
             * Get image for CVV
             * @returns {String}
             */
            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="' + $t('Card Verification Number Visual Reference') +
                    '" title="' + $t('Card Verification Number Visual Reference') +
                    '" />';
            },

            getVaultCCv: function(){
                return $('#zcredit_cc_vault_cc_cid').val();
            },

            getData: function () {
                var self = this;
                var data = this._super();
                data['additional_data']['cvv_vault'] = this.getVaultCCv() ;
                return data;

            }

        });
    }
);
