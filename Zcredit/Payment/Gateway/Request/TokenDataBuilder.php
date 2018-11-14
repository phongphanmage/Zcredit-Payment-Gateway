<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class TokenDataBuilder implements BuilderInterface
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
        $cvv = "";
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        if( $this->config->getValue('validate_cvv') )
        {
            if(
                isset($paymentAdditionalInformation['cvv_vault'])
                && $paymentAdditionalInformation['cvv_vault'] != ""
            )
            {
                $cvv = $paymentAdditionalInformation['cvv_vault'];
            }else
            {
                throw new \Magento\Framework\Exception\LocalizedException(__("Please input your CVV"));
            }
        }

        $extensionAttributes = $payment->getExtensionAttributes();

        $paymentToken = $extensionAttributes->getVaultPaymentToken();

        return [
            "CardNumber"            => $paymentToken->getGatewayToken(),
            "CVV"                   => $cvv,
            "ExpDate_MMYY"          => "",
        ];
    }
}