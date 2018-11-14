<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Model\Ui\Adminhtml;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Framework\View\Element\Template;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;

class TokenUiComponentProvider implements TokenUiComponentProviderInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * @param TokenUiComponentInterfaceFactory $componentFactory
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory
    ) {
        $this->componentFactory = $componentFactory;
    }

    /**
     * Get UI component for token
     *
     * @param PaymentTokenInterface $paymentToken
     * @return TokenUiComponentInterface
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = json_decode($paymentToken->getTokenDetails() ?: '{}', true);

        $component = $this->componentFactory->create(
            [
                'config' => [
                    'code' => \Zcredit\Payment\Gateway\Config\Config::VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $jsonDetails,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
                    'template' => 'Zcredit_Payment::form/vault.phtml'

                ],
                'name' => Template::class
            ]
        );

        return $component;
    }
}