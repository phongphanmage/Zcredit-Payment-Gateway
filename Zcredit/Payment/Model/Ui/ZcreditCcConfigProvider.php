<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Model\Ui;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Zcredit\Payment\Gateway\Config\Config;

class ZcreditCcConfigProvider implements ConfigProviderInterface
{
    const CODE = 'zcredit_cc';

    const VAULT_CODE = 'zcredit_cc_vault';

    /**
     * @var \Magento\Payment\Model\CcConfig
     */
    protected $ccConfig;

    protected $config;

    /**
     * @var \Magento\Payment\Model\MethodInterface[]
     */
    protected $methods = [];

    /**
     * @param \Magento\Payment\Model\CcConfig $ccConfig
     * @param PaymentHelper $paymentHelper
     * @param array $methodCodes
     */
    public function __construct(
        \Magento\Payment\Model\CcConfig $ccConfig,
        PaymentHelper $paymentHelper,
        Config $config,
        array $methodCodes = []
    ) {
        $this->ccConfig = $ccConfig;
        $this->config   = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $methodCode = \Zcredit\Payment\Gateway\Config\Config::PAYMENT_CODE;
        return [
            'payment' => [
                $methodCode => [
                    'availableTypes'        => [$methodCode => $this->getCcAvailableTypes()],
                    'months'                => [$methodCode => $this->getCcMonths()],
                    'years'                 => [$methodCode => $this->getCcYears()],
                    'hasVerification'       => [$methodCode => $this->hasVerification()],
                    'cvvImageUrl'           => [$methodCode => $this->getCvvImageUrl()],
                    'ccVaultCode'           => \Zcredit\Payment\Gateway\Config\Config::VAULT_CODE,
                    'hasInstallments'       => $this->config->getValue('installments'),
                    'installmentsNumber'    => $this->config->getValue('installments_number'),
                    'validate_cvv'          => $this->config->getValue('validate_cvv')
                ]
            ]
        ];
    }

    /**
     * Solo/switch card start years
     *
     * @return array
     * @deprecated 100.1.0 unused
     */
    protected function getSsStartYears()
    {
        return $this->ccConfig->getSsStartYears();
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        return $this->ccConfig->getCcMonths();
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        return $this->ccConfig->getCcYears();
    }

    /**
     * Retrieve CVV tooltip image url
     *
     * @return string
     */
    protected function getCvvImageUrl()
    {
        return $this->ccConfig->getCvvImageUrl();
    }

    /**
     * Retrieve availables credit card types
     *
     * @param string $methodCode
     * @return array
     */
    protected function getCcAvailableTypes()
    {
        $types = $this->ccConfig->getCcAvailableTypes();
        $availableTypes = $this->config->getAvailableCardTypes();
        if ($availableTypes) {
            foreach (array_keys($types) as $code) {
                if (!in_array($code, $availableTypes)) {
                    unset($types[$code]);
                }
            }
        }
        return $types;
    }

    /**
     * Retrieve has verification configuration
     *
     * @param string $methodCode
     * @return bool
     */
    protected function hasVerification()
    {
        $result = $this->ccConfig->hasVerification();
        $configData = $this->config->getValue('useccv');
        if ($configData !== null) {
            $result = (bool)$configData;
        }
        return $result;
    }

    /**
     * Whether switch/solo card type available
     *
     * @param string $methodCode
     * @return bool
     * @deprecated 100.1.0 unused
     */
    protected function hasSsCardType($methodCode)
    {
        $result = false;
        $availableTypes = explode(',', $this->methods[$methodCode]->getConfigData('cctypes'));
        $ssPresentations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && count($ssPresentations) > 0) {
            $result = true;
        }
        return $result;
    }
}