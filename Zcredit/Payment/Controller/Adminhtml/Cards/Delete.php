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

class Delete extends \Magento\Backend\App\Action
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
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CreditCardTokenFactory $paymentTokenFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CreditCardTokenFactory $paymentTokenFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->paymentTokenFactory = $paymentTokenFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $paymentTokenModel  = $this->paymentTokenFactory->create()->load($id);
        $customerId = $paymentTokenModel->getCustomerId();
        $paymentTokenModel->setIsActive(0);
        $paymentTokenModel->setIsVisible(0);
        try{
            $paymentTokenModel->save();
            $this->messageManager->addSuccessMessage(__('The Saved Credit Card Was Removed'));
        }catch(\Exception $e)
        {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->messageManager->addErrorMessage(__('Something went wrong when trying to delete'));
        }
        return $this->_redirect('customer/index/edit',['id'=> $customerId]);

    }
}