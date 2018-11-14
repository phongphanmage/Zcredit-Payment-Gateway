<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/07
 */

namespace Zcredit\Payment\Controller\Adminhtml\Cards;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Vault\Model\CreditCardTokenFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CreditCardTokenFactory
     */
    protected $paymentTokenFactory;

    /**
     * @var \Zcredit\Payment\Gateway\Config\Config
     */
    private $config;

    /**
     * @var \Zcredit\Payment\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CreditCardTokenFactory $paymentTokenFactory
     * @param \Zcredit\Payment\Gateway\Config\Config $config
     * @param \Zcredit\Payment\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CreditCardTokenFactory $paymentTokenFactory,
        \Zcredit\Payment\Gateway\Config\Config $config,
        \Zcredit\Payment\Helper\Data  $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->config = $config;
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response =  $this->_requestCheckCard();
        if(isset($response['CommitFullTransaction_TKResult']) && $response['CommitFullTransaction_TKResult'] == "true")
        {
            $save = $this->helper->saveVaultPaymentToken($response, $this->getRequest()->getParam('customerId'));

            if( $save )
            {
                $this->messageManager->addSuccessMessage(__('Your Credit Card Was Saved Successfully'));
                return $this->_redirect('customer/index/edit',['id' => $this->getRequest()->getParam('customerId')]);
            }else{
                $this->messageManager->addErrorMessage(__('Something went wrong with saving your credit cards'));
                return $this->_redirect('customer/index/edit',['id' => $this->getRequest()->getParam('customerId')]);
            }
        }
        else{
            $this->messageManager->addErrorMessage(__('Your credit card was REFUSED to save from Z-Credit Gateway, please re-check your card information'));
            return $this->_redirect('customer/index/edit',['id' => $this->getRequest()->getParam('customerId')]);
        }
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _requestCheckCard()
    {
        $customerId = $this->getRequest()->getParam('customerId');

        $terminalNumber = $this->config->getTerminalNumber();

        $terminalPassword = $this->config->getPassword();

        $params = $this->getRequest()->getParams();

        $request = [
            "J"                         => 2,
            "CVV"                       => $params['CVV'],
            "CardNumber"                => $params['CardNumber'],
            "ExpDate_MMYY"              => str_pad($params['cc_exp_month'], 2, "0", STR_PAD_LEFT) . substr($params['cc_exp_year'], 2, 2),
            "CustomerName"              => $this->customerRepository->getById($customerId)->getFirstname() . ' ' . $this->customerRepository->getById($customerId)->getLastname(),
            "CustomerAddress"           => "",
            "PhoneNumber"               => "",
            "NumOfPayments"             => 0,
            "FirstPaymentSum"           => 0,
            "OtherPaymentsSum"          => 0,
            "IsRefund"                  => false,
            "IsPointsTransaction"       => false,
            "IsCustomerPresent"         => false,
            "Track2"                    => "",
            "PointsSum"                 => 0.00,
            "AuthNum"                   => "",
            "CreditClubID"              => "",
            "HolderID"                  => "",
            "ExtraData"                 => "",
            "CreditType"                => 1,
            "CurrencyType"              => 2,
            "TransactionSum"            => number_format(1, 2, '.', ''),
            "TerminalNumber"            => $terminalNumber,
            "Password"                  => $terminalPassword,
        ];

        $soapClient = new \Zcredit\Payment\Gateway\Http\Client\lib\Soap\ZcreditClient(\Zcredit\Payment\Gateway\Config\Config::PAYMENT_URI, 'wsdl');

        $soapClient->soap_defencoding = 'UTF-8';

        $soapClient->decode_utf8 = false;

        $response = $soapClient->call('CommitFullTransaction_TK', $request);

        return $response;
    }
}