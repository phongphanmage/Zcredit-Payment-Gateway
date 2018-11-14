<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class VaultDataBuilder implements BuilderInterface
{
    /**
     * Additional options in request to gateway
     */
    const OPTIONS = 'zcredit_save_card';

    /**
     * The option that determines whether the payment method associated with
     * the successful transaction should be stored in the Vault.
     */
    const STORE_IN_VAULT_ON_SUCCESS = 'storeInVaultOnSuccess';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $saveToken = false;
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        $paymentAdditionalInformation = $payment->getAdditionalInformation();
        if(
            isset($paymentAdditionalInformation['is_active_payment_token_enabler'])
                && $paymentAdditionalInformation['is_active_payment_token_enabler'] != null
        )
        {
            $saveToken = true;
        }
        return [
            self::OPTIONS => $saveToken
        ];
    }
}