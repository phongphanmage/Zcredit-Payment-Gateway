<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;

class CaptureCommentHistoryHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     * @return $this
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);

        $payment = $payment->getPayment();

        $installmentNumber = 'No Installment';
        $isIntantPurchase = 'FALSE';

        if (isset($response['Validation_Result_Code'])) {
            $responseCode = $response['Validation_Result_Code'];
        } else {

            if (isset($response['response'])) {
                $responseCode = $response['response'];
            } else {
                $responseCode = "";
            }
        }

        if (isset($response['ReferenceNumber'])) {
            $referenceNumber = $response['ReferenceNumber'];
        } else {
            $referenceNumber = "";
        }

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        if(
            isset($paymentAdditionalInformation['number_of_installments'])
            && $paymentAdditionalInformation['number_of_installments']!= 0

        )
        {
            $installmentNumber = $paymentAdditionalInformation['number_of_installments'];
        }
        if(
            isset($paymentAdditionalInformation['instant-purchase'])
            && $paymentAdditionalInformation['instant-purchase'] == "true"
            && $payment->getMethodInstance()->getCode() == \Zcredit\Payment\Gateway\Config\Config::VAULT_CODE

        )
        {
            $isIntantPurchase =  'TRUE';
        }

        $type = 'Z-Credit Capture Response:';

        $comment = __('%1 <br />
                        Result Code is : %2 <br />
                        Validation Result Message : %3 <br />
                        ReferenceNumber: %4 <br /> 
                        AuthNum: %5 <br /> 
                        CardBrandCode: %6 <br />
                        CardFinancerCode: %7 <br />
                        CardIssuerCode: %8 <br />
                        CardName: %9 <br />
                        CardNumber: %10 <br />
                        ExpiredDate (MM/YY): %11 <br />
                        VoucherNumber: %12 <br />
                        InstallmentNumber: %13 <br />
                        IsIntantPurchase: %14 <br />',
            $type,
            $responseCode,
            ($response['Validation_Result_Message'])?$response['Validation_Result_Message']:'NULL',
            $referenceNumber,
            $response['AuthNum'],
            $response['CardBrandCode'],
            $response['CardFinancerCode'],
            $response['CardIssuerCode'],
            $response['CardName'],
            'xxxx-xxxx-'.substr($response['CardNumber'], -4),
            $response['ExpDate_MMYY'],
            $response['VoucherNumber'],
            $installmentNumber,
            $isIntantPurchase
        );

        $payment->getOrder()->addStatusHistoryComment($comment);

        return $this;
    }
}