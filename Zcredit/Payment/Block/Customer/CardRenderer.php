<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/18
 */

namespace Zcredit\Payment\Block\Customer;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Block\AbstractCardRenderer;

class CardRenderer extends AbstractCardRenderer
{
    /**
     * Can render specified token
     *
     * @param PaymentTokenInterface $token
     * @return boolean
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return $token->getPaymentMethodCode() === \Zcredit\Payment\Model\Ui\ZcreditCcConfigProvider::CODE;
    }

    /**
     * @return string
     */
    public function getNumberLast4Digits()
    {
        return $this->getTokenDetails()['CardNumber'];
    }

    /**
     * @return string
     */
    public function getExpDate()
    {
        return $this->getTokenDetails()['expirationDate'];
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        if( $this->getTokenDetails()['type'] != null )
        {

            return $this->getIconForType(strtoupper($this->getTokenDetails()['type']))['url'];
        }else{
            return null;
        }


    }

    /**
     * @return int
     */
    public function getIconHeight()
    {
        if( $this->getTokenDetails()['type'] != null )
        {
            return $this->getIconForType(strtoupper($this->getTokenDetails()['type']))['height'];
        }else{
            return null;
        }
    }

    /**
     * @return int
     */
    public function getIconWidth()
    {
        if( $this->getIconForType($this->getTokenDetails()['type']) != null )
        {
            return $this->getIconForType(strtoupper($this->getTokenDetails()['type']))['width'];
        }else{
            return null;
        }
    }
}
