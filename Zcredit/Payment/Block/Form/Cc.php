<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Block\Form;
use Magento\Payment\Helper\Data;

class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var string
     */
    protected $_template = 'Zcredit_Payment::form/cc.phtml';

    /**
     * @var Data
     */
    private $paymentDataHelper;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $sessionQuote;

    /**
     * Cc constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param Data $paymentDataHelper
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        Data $paymentDataHelper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = []
    )
    {
        parent::__construct($context, $paymentConfig, $data);
        $this->paymentDataHelper = $paymentDataHelper;
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * Check if vault enabled
     * @return bool
     */
    public function isVaultEnabled()
    {
        $vaultPayment = $this->getVaultPayment();
        return $vaultPayment->isActive($this->sessionQuote->getStoreId());
    }

    /**
     * Get configured vault payment for Braintree
     * @return VaultPaymentInterface
     */
    private function getVaultPayment()
    {
        return $this->paymentDataHelper->getMethodInstance(\Zcredit\Payment\Gateway\Config\Config::VAULT_CODE);
    }
}