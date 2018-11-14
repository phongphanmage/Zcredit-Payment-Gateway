<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/16
 */

namespace Zcredit\Payment\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\CreditCardTokenFactory;
use Magento\Framework\Encryption\EncryptorInterface;
class Data extends AbstractHelper
{
    /**
     * @var PaymentTokenInterfaceFactory
     */
    protected $paymentTokenFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    private $encryptor;

    public function __construct(
        Context $context,
        CreditCardTokenFactory $paymentTokenFactory,
        EncryptorInterface $encryptor,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        parent::__construct($context);
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->encryptor           = $encryptor;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * Get vault payment token entity
     */
    public function saveVaultPaymentToken($response, $customerId)
    {
        // Check token existing in gateway response
        $token = $response['Token'];
        if (empty($token)) {
            return null;
        }

        $expirationDate = $this->getExpirationDate($response);
        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create();

        $paymentToken->setGatewayToken($token);
        $paymentToken->setCustomerId($customerId);
        $paymentToken->setPaymentMethodCode(\Zcredit\Payment\Gateway\Config\Config::PAYMENT_CODE);
        $paymentToken->setPublicHash($this->encryptor->hash($token));

        $paymentToken->setExpiresAt($expirationDate->format('Y-m-d 00:00:00'));

        if( isset($response['CardNumber']) && isset($response['CardName']))
        {
            $paymentToken->setTokenDetails($this->convertDetailsToJSON([
                'CardNumber'     => '-xxxx-'.substr($response['CardNumber'], -4),
                'expirationDate' => $expirationDate->format('m/Y'),
                'maskedCC'       =>  '-xxxx-'.substr($response['CardNumber'], -4),
                'type'           => ($this->detectCardType($response['CardNumber']))?$this->detectCardType($response['CardNumber']):null,
            ]));
        }
        try{
            $paymentToken->save();
            return true;
        }catch (\Exception $e)
        {
            throw new $e;
        }
    }

    /**
     * Convert payment token details to JSON
     * @param array $details
     * @return string
     */
    public function convertDetailsToJSON($details)
    {
        $json = $this->serializer->serialize($details);
        return $json ? $json : '{}';
    }

    public function convertJsonToDetail($json)
    {
        $json = $this->serializer->unserialize($json);
        return $json ? $json : '{}';
    }

    public function getExpirationDate($response)
    {
        if( isset($response['ExpDate_MMYY']) )
        {
            $mmyy = explode('/', $response['ExpDate_MMYY']);
            if(isset($mmyy[0]) && isset($mmyy[1]) )
            {
                $month = $mmyy[0];
                $year = $mmyy[1];
                return new \DateTime(
                    $year
                    . '-'
                    . $month
                    . '-'
                    . '01'
                    . ' '
                    . '00:00:00',
                    new \DateTimeZone('UTC')
                );
            }
        }
    }

    public function detectCardType($num)
    {
        $re = array(
            "visa"       => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex"       => "/^3[47][0-9]{13}$/",
            "discover"   => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
        );

        if (preg_match($re['visa'],$num))
        {
            return 'vi';
        }
        else if (preg_match($re['mastercard'],$num))
        {
            return 'mc';
        }
        else if (preg_match($re['amex'],$num))
        {
            return 'ae';
        }
        else if (preg_match($re['discover'],$num))
        {
            return 'di';
        }
        else
        {
            return false;
        }
    }


}