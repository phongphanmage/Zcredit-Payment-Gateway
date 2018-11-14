<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class CustomerDataBuilder implements BuilderInterface
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
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();

        $billingAddress = $order->getBillingAddress();

        return [
            "CustomerName"          => $billingAddress->getFirstname().' '.$billingAddress->getLastname(),
            "CustomerAddress"       => $billingAddress->getStreetLine1().' '.$billingAddress->getPostcode(),
            "PhoneNumber"           => "",
        ];
    }
}