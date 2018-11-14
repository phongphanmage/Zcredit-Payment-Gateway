<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/07
 */
namespace Zcredit\Payment\Block\Adminhtml\Cards\News\Tab;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;
    protected $config;


    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Zcredit\Payment\Model\Ui\ZcreditCcConfigProvider $config,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->config = $config;


        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('zcredit_ad_cards_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Credit Card Information')]);

        $fieldset->addField('id', 'hidden', ['name' => 'id']);


        $fieldset->addField('CardNumber', 'text', [
            'name'      => 'CardNumber',
            'required'  =>  true,
            'class'     => 'validate-number',
            'label'     =>  __('Card Number'),
        ]);

        $fieldset->addField('cc_exp_month', 'select', [
            'name'      => 'cc_exp_month',
            'required'  =>  true,
            'options'   => $this->config->getCcMonths(),
            'label'     =>  __('Expired Month'),
        ]);

        $fieldset->addField('cc_exp_year', 'select', [
            'name'      => 'cc_exp_year',
            'required'  =>  true,
            'options'   => $this->config->getCcYears(),
            'label'     =>  __('Expired Year'),
        ]);

        $fieldset->addField('CVV', 'text', [
            'name'      => 'CVV',
            'required'  =>  true,
            'class'     => 'validate-number',
            'label'     =>  __('Card Verification Number'),
        ]);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
