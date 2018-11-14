define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'zcredit_cc',
                component: 'Zcredit_Payment/js/view/payment/method-renderer/zcredit_cc'
            }
        );
        return Component.extend({});
    });
