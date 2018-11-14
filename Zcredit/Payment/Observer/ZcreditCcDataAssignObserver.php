<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;

class ZcreditCcDataAssignObserver extends AbstractDataAssignObserver
{
    /**
     *
     */
    const CC_ID = 'cc_cid';
    /**
     *
     */
    const CC_SS_START_MONTH = 'cc_ss_start_month';
    /**
     *
     */
    const CC_SS_START_YEAR = 'cc_ss_start_year';
    /**
     *
     */
    const CC_SS_ISSUE = 'cc_ss_issue';
    /**
     *
     */
    const CC_TYPE = 'cc_type';
    /**
     *
     */
    const CC_EXP_YEAR = 'cc_exp_year';
    /**
     *
     */
    const CC_EXP_MONTH = 'cc_exp_month';
    /**
     *
     */
    const CC_NUMBER = 'cc_number';
    /**
     *
     */
    const NUMBER_INSTALLMENTS = 'number_of_installments';
    /**
     *
     */
    const VAULT_CVV = 'cvv_vault';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::CC_ID,
        self::CC_SS_START_MONTH,
        self::CC_SS_START_YEAR,
        self::CC_SS_ISSUE,
        self::CC_TYPE,
        self::CC_EXP_MONTH,
        self::CC_EXP_YEAR,
        self::CC_NUMBER,
        self::NUMBER_INSTALLMENTS,
        self::VAULT_CVV

    ];
    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        if( isset($additionalData['cc_type']) )
        {
            $paymentInfo->setCcType($additionalData['cc_type']);
        }
        if( isset($additionalData['cc_number']) )
        {
            $paymentInfo->setCcLast4(substr($additionalData['cc_number'], -4));
        }
        if( isset($additionalData['cc_exp_month']) )
        {
            $paymentInfo->setCcExpMonth($additionalData['cc_exp_month']);
        }
        if( isset($additionalData['cc_exp_year']) )
        {
            $paymentInfo->setCcExpYear($additionalData['cc_exp_year']);
        }

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
        return;

    }
}