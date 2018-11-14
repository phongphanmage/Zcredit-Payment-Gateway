<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/07
 */
namespace Zcredit\Payment\Block\Adminhtml\Cards;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;

class News extends FormContainer
{
    protected $_mode = 'news';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_cards';
        $this->_blockGroup = 'Zcredit_Payment';

        parent::_construct();
        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('customer/index/edit',
                        ['id' => $this->getRequest()->getParam('customerId')]) . '\')',
                'class' => 'back'
            ],
            -1
        );
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $title = __("Add new credit cards");

        return $title;
    }

}
