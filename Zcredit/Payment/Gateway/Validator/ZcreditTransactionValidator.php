<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Validator;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ZcreditTransactionValidator extends AbstractValidator
{

    protected $config;

    public function __construct(
        ResultInterfaceFactory $resultFactory,
        \Zcredit\Payment\Gateway\Config\Config $config
    )
    {
        parent::__construct($resultFactory);
        $this->config = $config;
    }

    protected $failedCode =[];

    public function validate(array $validationSubject)
    {
        $response = \Magento\Payment\Gateway\Helper\SubjectReader::readResponse($validationSubject);

        $isValid = true;

        $errorMessages = [];

        if(
            isset($response['CommitFullTransactionResult']) && $response['CommitFullTransactionResult'] == "false"
        )
        {
            $this->sendFailedEmail($response['Validation_Result_Message']);
            throw new \Magento\Framework\Exception\LocalizedException(__($this->config->getValue('error_message')));
        }
        if(
            isset($response['CommitFullTransaction_TKResult']) && $response['CommitFullTransaction_TKResult'] == "false"
        )
        {
            $this->sendFailedEmail( $response['Validation_Result_Message']);
            throw new \Magento\Framework\Exception\LocalizedException(__($this->config->getValue('error_message')));
        }

        return $this->createResult($isValid, $errorMessages);
    }

    private function sendFailedEmail($message)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get(\Magento\Checkout\Helper\Data::class)
            ->sendPaymentFailedEmail($objectManager->get(\Magento\Checkout\Model\Type\Onepage::class)
                ->getQuote(), $message);
        return;
    }
}