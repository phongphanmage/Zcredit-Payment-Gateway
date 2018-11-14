<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;

class AuthorizedCommentHistoryHandler implements HandlerInterface
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

        $type = 'Z-Credit Authorized Response:';

        $comment = __('%1 <br />Result code is : %2 <br /> ReferenceNumber: %3 ',
            $type, $responseCode, $referenceNumber);

        $payment->getOrder()->addStatusHistoryComment($comment);

        return $this;
    }
}