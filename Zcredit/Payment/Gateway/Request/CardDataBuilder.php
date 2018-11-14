<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class CardDataBuilder implements BuilderInterface
{
    /**
     * @var \Zcredit\Payment\Gateway\Config\Config
     */
    private $config;


    /**
     * @var \Zcredit\Payment\Helper\Data
     */
    protected $helper;

    /**
     * @param \Zcredit\Payment\Gateway\Config\Config
     * @param \Zcredit\Payment\Helper\Data
     */
    public function __construct(
        \Zcredit\Payment\Gateway\Config\Config $config,
        \Zcredit\Payment\Helper\Data           $helper
    ) {
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $cvv = $cardNumber = $expDate = null;
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        if(
            isset($paymentAdditionalInformation['cc_cid']) &&
            isset($paymentAdditionalInformation['cc_number']) &&
            isset($paymentAdditionalInformation['cc_exp_month'])  &&
            isset($paymentAdditionalInformation['cc_exp_year'])
        )
        {
            $cvv          = $paymentAdditionalInformation['cc_cid'];
            $cardNumber   = $paymentAdditionalInformation['cc_number'];
            $expDate      = str_pad($paymentAdditionalInformation['cc_exp_month'] ,2,"0",STR_PAD_LEFT).substr($paymentAdditionalInformation['cc_exp_year'] ,2,2);
        }
        return [
            "CVV"                   => $cvv,
            "CardNumber"            => $cardNumber,
            "ExpDate_MMYY"          => $expDate,
        ];


    }
}