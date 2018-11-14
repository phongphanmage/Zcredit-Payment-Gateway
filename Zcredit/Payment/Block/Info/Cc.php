<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Block\Info;

class Cc extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var string
     */
    protected $_template = 'Zcredit_Payment::info/zcredit_cc.phtml';
}