<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentDataBuilder implements BuilderInterface
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
     * RecurringDataBuilder constructor.
     *
     * @param \Zcredit\Payment\Gateway\Config\Config
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
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        $creditType = 1;
        if(
            isset($paymentAdditionalInformation['number_of_installments'])
            && $paymentAdditionalInformation['number_of_installments']!= 0

        )
        {
            $creditType = 8;
        }

        return [
                "IsRefund"              => false,
                "IsPointsTransaction"   => false,
                "IsCustomerPresent"     => false,
                "Track2"                => "",
                "PointsSum"             => 0.00,
                "AuthNum"               => "",
                "CreditClubID"          => "",
                "HolderID"              => "",
                "ExtraData"             => "",
                "CreditType"            => $creditType,
                "CurrencyType"          => 1,
            ];
    }
}