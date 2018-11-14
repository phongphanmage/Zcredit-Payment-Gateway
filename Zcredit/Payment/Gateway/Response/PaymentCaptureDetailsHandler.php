<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentCaptureDetailsHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);

        $payment = $payment->getPayment();

        $payment->setCcTransId($response['ReferenceNumber']);

        $payment->setTransactionId($response['ReferenceNumber']);

        $payment->setLastTransId($response['ReferenceNumber']);

        $payment->setIsTransactionClosed(true);

        $payment->setShouldCloseParentTransaction(false);
    }
}