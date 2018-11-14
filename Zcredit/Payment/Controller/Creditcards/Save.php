<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/06
 */

namespace Zcredit\Payment\Controller\Creditcards;
use Magento\Framework\App\RequestInterface;

class Save  extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Zcredit\Payment\Gateway\Config\Config
     */
    private $config;

    /**
     * @var \Zcredit\Payment\Helper\Data
     */
    private $helper;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Zcredit\Payment\Gateway\Config\Config $config
     * @param \Zcredit\Payment\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Zcredit\Payment\Gateway\Config\Config $config,
        \Zcredit\Payment\Helper\Data  $helper
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->redirectFactory = $context->getResultRedirectFactory();
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response =  $this->_requestCheckCard();
        if(isset($response['CommitFullTransaction_TKResult']) && $response['CommitFullTransaction_TKResult'] == "true")
        {
            $save = $this->helper->saveVaultPaymentToken($response, $this->customerSession->getCustomer()->getId());

            if( $save )
            {
                $this->messageManager->addSuccessMessage(__('Your Credit Card Was Saved Successfully'));
                return $this->_redirect('vault/cards/listaction');
            }else{
                $this->messageManager->addErrorMessage(__('Something went wrong with saving your credit cards'));
                return $this->_redirect('vault/cards/listaction');
            }
        }
        else{
            $this->messageManager->addErrorMessage(__('Your credit card was REFUSED to save from Z-Credit Gateway, please re-check your card information'));
            return $this->_redirect('zcredit/creditcards/add');
        }

    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * @return mixed
     */
    private function _requestCheckCard()
    {
        $terminalNumber = $this->config->getTerminalNumber();

        $terminalPassword = $this->config->getPassword();

        $params = $this->getRequest()->getParams();

        $request = [
            "J"                         => 2,
            "CVV"                       => $params['CVV'],
            "CardNumber"                => $params['CardNumber'],
            "ExpDate_MMYY"              => str_pad($params['cc_exp_month'], 2, "0", STR_PAD_LEFT) . substr($params['cc_exp_year'], 2, 2),
            "CustomerName"              => $this->customerSession->getCustomer()->getFirstname() . ' ' . $this->customerSession->getCustomer()->getLastname(),
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