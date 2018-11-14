<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class InstallmentsDataBuilder implements BuilderInterface
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
        $numOfPayments = $firstPaymentSum = $otherPaymentsSum = 0;

        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $sum =  \Magento\Payment\Gateway\Helper\SubjectReader::readAmount($buildSubject);

        $payment = $paymentDataObject->getPayment();

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        if(
            isset($paymentAdditionalInformation['number_of_installments'])
            && $paymentAdditionalInformation['number_of_installments']!= 0

        )
        {
            $numOfPayments           = $paymentAdditionalInformation['number_of_installments'];
            $firstPaymentSum         = $this->getFirstPaymentSum($sum, $numOfPayments);
            $otherPaymentsSum        = $this->getOtherPaymentSum($sum, $numOfPayments);
        }
        return [
                "NumOfPayments"         => $numOfPayments,
                "FirstPaymentSum"       => $firstPaymentSum,
                "OtherPaymentsSum"      => $otherPaymentsSum,
            ];
    }


    protected function getFirstPaymentSum( $sum, $numOfPayments)
    {
        $otherPaymentSum = $this->getOtherPaymentSum($sum, $numOfPayments);

        $tempSum         = (float)($otherPaymentSum)*($numOfPayments-1);

        return round(($sum - $tempSum)*100)/100;
    }

    protected function getOtherPaymentSum( $sum, $numOfPayments)
    {
        return floor(($sum/$numOfPayments)*100)/100;
    }

}