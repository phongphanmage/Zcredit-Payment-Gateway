<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/11/06
 */

namespace Zcredit\Payment\Block\Customer;

use Magento\Framework\View\Element\Template;

class SaveCard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Zcredit\Payment\Model\Ui\ZcreditCcConfigProvider
     */
    protected $config;

    /**
     * SaveCard constructor.
     * @param Template\Context $context
     * @param \Zcredit\Payment\Model\Ui\ZcreditCcConfigProvider $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Zcredit\Payment\Model\Ui\ZcreditCcConfigProvider $config,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->config  = $config;
    }

    /**
     * @return array
     */
    public function getExpYears()
    {
        return $this->config->getCcYears();
    }

    /**
     * @return array
     */
    public function getExpMonths()
    {
        return $this->config->getCcMonths();
    }
}
