<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class TerminalAccountDataBuilder implements BuilderInterface
{
    /**
     * @var \Zcredit\Payment\Gateway\Config\Config
     */
    private $config;

    /**
     * @param \Zcredit\Payment\Gateway\Config\Config
     */
    public function __construct(
        \Zcredit\Payment\Gateway\Config\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();

        $storeId = $order->getStoreId();

        $terminalNumber = $this->config->getTerminalNumber($storeId);

        $terminalPassword = $this->config->getPassword($storeId);

        return [
            "TerminalNumber"        => $terminalNumber,
            "Password"              => $terminalPassword,
        ];
    }
}