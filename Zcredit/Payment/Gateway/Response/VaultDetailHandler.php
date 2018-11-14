<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Gateway\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;

use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\Data\PaymentTokenInterfaceFactory;

class VaultDetailHandler implements HandlerInterface
{
    /**
     * @var PaymentTokenInterfaceFactory
     */
    protected $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    protected $paymentExtensionFactory;


    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;
    private $helper;

    public function __construct(
        PaymentTokenInterfaceFactory $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        \Zcredit\Payment\Helper\Data  $helper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->helper   = $helper;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }
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
            $responseCode = "";
        }
        if (isset($response['Token']))
        {
            $token = $response['Token'];
        } else {
            $token = null;
            return $this;
        }

        if( null !==  $token )
        {
            $paymentToken = $this->getVaultPaymentToken($response);
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);

            $type = 'Z-Credit Saved Card Response:';

            $comment = __('%1 <br />Result code is : %2 <br /> Saved customer\'s card',
                $type, $responseCode);

            $payment->getOrder()->addStatusHistoryComment($comment);
        }

        return $this;
    }

    /**
     * Get vault payment token entity
     */
    protected function getVaultPaymentToken($response)
    {
        // Check token existing in gateway response
        $token = $response['Token'];
        if (empty($token)) {
            return null;
        }

        $expirationDate = $this->helper->getExpirationDate($response);
        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create();

        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($expirationDate->format('Y-m-d 00:00:00'));

        if( isset($response['CardNumber']) && isset($response['CardName']))
        {
            $paymentToken->setTokenDetails($this->helper->convertDetailsToJSON([
                'CardNumber'     => '-xxxx-'.substr($response['CardNumber'], -4),
                'expirationDate' => $expirationDate->format('m/Y'),
                'maskedCC'       =>  '-xxxx-'.substr($response['CardNumber'], -4),
                'type'           => ($this->helper->detectCardType($response['CardNumber']))?$this->helper->detectCardType($response['CardNumber']):null,
            ]));
        }

        return $paymentToken;
    }

    /**
     * Get payment extension attributes
     * @param InfoInterface $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}

