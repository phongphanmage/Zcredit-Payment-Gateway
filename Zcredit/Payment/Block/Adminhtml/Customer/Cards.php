<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/07
 */

namespace Zcredit\Payment\Block\Adminhtml\Customer;
use Magento\Backend\Block\Template;

class Cards extends Template
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    private $customerRepository;

    private $paymentTokenFactory;

    private $helper;

    private $configProvider;

    /**
     * @param Template\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository

     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Vault\Model\PaymentTokenFactory   $paymentTokenFactory,
        \Zcredit\Payment\Helper\Data               $helper,
        \Magento\Payment\Model\CcConfigProvider $configProvider,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->customerRepository = $customerRepository;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->helper              = $helper;
        $this->configProvider      = $configProvider;

    }
    /**
     * Return cards list.
     */
    public function getCards()
    {
        $customerId = $this->getRequest()->getParam('id');

        if ($customerId) {
            $cards = $this->paymentTokenFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('is_visible', 1);
            return $cards;
        }
        return [];
    }

    public function decodeDetail($json)
    {
        return $this->helper->convertJsonToDetail($json);
    }

    public function getIcons()
    {
        return $icons = $this->configProvider->getIcons();
    }
}