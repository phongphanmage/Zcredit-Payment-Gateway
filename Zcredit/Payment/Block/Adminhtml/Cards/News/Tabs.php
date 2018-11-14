<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/07
 */
namespace Zcredit\Payment\Block\Adminhtml\Cards\News;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('zcredit_ad_cards_edit_tabs');
        $this->setDestElementId('edit_form');
    }
}
