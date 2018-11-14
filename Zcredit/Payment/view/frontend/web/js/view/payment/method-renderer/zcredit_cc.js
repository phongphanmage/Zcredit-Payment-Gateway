/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'underscore',
    'jquery',
    'Magento_Payment/js/view/payment/cc-form',
    'Magento_Payment/js/model/credit-card-validation/credit-card-data',
    'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Vault/js/view/payment/vault-enabler',
    'Zcredit_Payment/js/model/installments',
], function (_, $,Component, creditCardData, cardNumberValidator, $t, additionalValidators, VaultEnabler, installments) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Zcredit_Payment/payment/zcredit_cc',
            creditCardType: '',
            creditCardExpYear: '',
            creditCardExpMonth: '',
            creditCardNumber: '',
            creditCardSsStartMonth: '',
            creditCardSsStartYear: '',
            creditCardSsIssue: '',
            creditCardVerificationNumber: '',
            selectedCardType: null,
            installment: ''
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe([
                    'creditCardType',
                    'creditCardExpYear',
                    'creditCardExpMonth',
                    'creditCardNumber',
                    'creditCardVerificationNumber',
                    'creditCardSsStartMonth',
                    'creditCardSsStartYear',
                    'creditCardSsIssue',
                    'selectedCardType',
                    'installment'
                ]);

            return this;
        },
        getInstallments: installments.getInstallments(),
        /**
         * Init component
         */
        initialize: function () {
            var self = this;

            this._super();

            this.vaultEnabler = new VaultEnabler();

            this.vaultEnabler.setPaymentCode(this.getVaultCode());

            //Set credit card number to credit card data object
            this.creditCardNumber.subscribe(function (value) {
                var result;

                self.selectedCardType(null);

                if (value === '' || value === null) {
                    return false;
                }
                result = cardNumberValidator(value);

                if (!result.isPotentiallyValid && !result.isValid) {
                    return false;
                }

                if (result.card !== null) {
                    self.selectedCardType(result.card.type);
                    creditCardData.creditCard = result.card;
                }

                if (result.isValid) {
                    creditCardData.creditCardNumber = value;
                    self.creditCardType(result.card.type);
                }
            });

            //Set expiration year to credit card data object
            this.creditCardExpYear.subscribe(function (value) {
                creditCardData.expirationYear = value;
            });

            //Set expiration month to credit card data object
            this.creditCardExpMonth.subscribe(function (value) {
                creditCardData.expirationMonth = value;
            });

            //Set cvv code to credit card data object
            this.creditCardVerificationNumber.subscribe(function (value) {
                creditCardData.cvvCode = value;
            });

            installments.setInstallments(12);

            return this;
        },

        /**
         * Get code
         * @returns {String}
         */
        getCode: function () {
            return 'zcredit_cc';
        },

        /**
         * Get data
         * @returns {Object}
         */
        getData: function () {
            var data =  {
                'method': this.item.method,
                'additional_data': {
                    'cc_cid': this.creditCardVerificationNumber(),
                    'cc_ss_start_month': this.creditCardSsStartMonth(),
                    'cc_ss_start_year': this.creditCardSsStartYear(),
                    'cc_ss_issue': this.creditCardSsIssue(),
                    'cc_type': this.creditCardType(),
                    'cc_exp_year': this.creditCardExpYear(),
                    'cc_exp_month': this.creditCardExpMonth(),
                    'cc_number': this.creditCardNumber(),
                    'number_of_installments': this.installment()
                }
            };
            this.vaultEnabler.visitAdditionalData(data);
            return data;
        },

        /**
         * Get list of available credit card types
         * @returns {Object}
         */
        getCcAvailableTypes: function () {
            return window.checkoutConfig.payment.ccform.availableTypes[this.getCode()];
        },

        /**
         * Get payment icons
         * @param {String} type
         * @returns {Boolean}
         */
        getIcons: function (type) {
            return window.checkoutConfig.payment.ccform.icons.hasOwnProperty(type) ?
                window.checkoutConfig.payment.ccform.icons[type]
                : false;
        },

        /**
         * Get list of months
         * @returns {Object}
         */
        getCcMonths: function () {
            return window.checkoutConfig.payment.ccform.months[this.getCode()];
        },

        /**
         * Get list of years
         * @returns {Object}
         */
        getCcYears: function () {
            return window.checkoutConfig.payment.ccform.years[this.getCode()];
        },

        /**
         * Check if current payment has verification
         * @returns {Boolean}
         */
        hasVerification: function () {
            return window.checkoutConfig.payment.ccform.hasVerification[this.getCode()];
        },

        /**
         * @deprecated
         * @returns {Boolean}
         */
        hasSsCardType: function () {
            return window.checkoutConfig.payment.ccform.hasSsCardType[this.getCode()];
        },

        /**
         * Get image url for CVV
         * @returns {String}
         */
        getCvvImageUrl: function () {
            return window.checkoutConfig.payment.ccform.cvvImageUrl[this.getCode()];
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


        getSsStartYears: function () {
            return window.checkoutConfig.payment.zcredit_cc.ssStartYears[this.getCode()];
        },

        /**
         * Get list of available credit card types values
         * @returns {Object}
         */
        getCcAvailableTypesValues: function () {
            return _.map(this.getCcAvailableTypes(), function (value, key) {
                return {
                    'value': key,
                    'type': value
                };
            });
        },

        /**
         * Get list of available month values
         * @returns {Object}
         */
        getCcMonthsValues: function () {
            return _.map(this.getCcMonths(), function (value, key) {
                return {
                    'value': key,
                    'month': value
                };
            });
        },

        /**
         * Get list of available year values
         * @returns {Object}
         */
        getCcYearsValues: function () {
            return _.map(this.getCcYears(), function (value, key) {
                return {
                    'value': key,
                    'year': value
                };
            });
        },

        /**
         * @deprecated
         * @returns {Object}
         */
        getSsStartYearsValues: function () {
            return _.map(this.getSsStartYears(), function (value, key) {
                return {
                    'value': key,
                    'year': value
                };
            });
        },

        /**
         * Is legend available to display
         * @returns {Boolean}
         */
        isShowLegend: function () {
            return false;
        },

        /**
         * Get available credit card type by code
         * @param {String} code
         * @returns {String}
         */
        getCcTypeTitleByCode: function (code) {
            var title = '',
                keyValue = 'value',
                keyType = 'type';

            _.each(this.getCcAvailableTypesValues(), function (value) {
                if (value[keyValue] === code) {
                    title = value[keyType];
                }
            });

            return title;
        },

        /**
         * Prepare credit card number to output
         * @param {String} number
         * @returns {String}
         */
        formatDisplayCcNumber: function (number) {
            return 'xxxx-' + number.substr(-4);
        },

        /**
         * Get credit card details
         * @returns {Array}
         */
        getInfo: function () {
            return [
                {
                    'name': 'Credit Card Type', value: this.getCcTypeTitleByCode(this.creditCardType())
                },
                {
                    'name': 'Credit Card Number', value: this.formatDisplayCcNumber(this.creditCardNumber())
                }
            ];
        },

        /**
         * Check if payment is active
         *
         * @returns {Boolean}
         */
        isActive: function () {
            return true;
        },

        /**
         * @returns {Boolean}
         */
        isVaultEnabled: function () {
            return this.vaultEnabler.isVaultEnabled();
        },

        /**
         * @returns {String}
         */
        getVaultCode: function () {
            return 'zcredit_cc_vault';
        },

        hasInstallments: function() {
            return window.checkoutConfig.payment.zcredit_cc.hasInstallments;
        },

        validate: function () {
            var formText = 'form[data-role=zcredit-cc-form]';

            var expiration = Boolean($(formText + ' #zcredit_cc_expiration').valid());
            var expiration_yr = Boolean($(formText + ' #zcredit_cc_expiration_yr').valid());

            if(!expiration || !expiration_yr) {
                return false;
            }

            return true;
        },
        /**
         * @override
         */
        placeOrder: function() {
            if (additionalValidators.validate() && this.validate()) {
                this.isPlaceOrderActionAllowed(false);
                this._super();
            }
        }

    });
});
