define(
    [
        'ko',
    ],
    function (ko) {
        'use strict';
        var installments = ko.observableArray([]);
        return {

            getInstallmentsNumber: function(){
                return window.checkoutConfig.payment.zcredit_cc.installmentsNumber;
            },

            /**
             * Populate the list of installments
             * @param {Array} methods
             */
            setInstallments: function () {
                var i;
                var installmentsNumber = this.getInstallmentsNumber();

                installments.push({
                    key: 'No Installment',
                    value: 0
                });

                for (i = 2; i <= installmentsNumber; i++) {
                    installments.push({
                        key: i,
                        value: i
                    });
                }
            },
            /**
             * Get the list of available installments.
             * @returns {Array}
             */
            getInstallments: function () {
                return installments;
            }


        };
    }
);
