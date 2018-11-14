<?php

/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/15
 */

namespace Zcredit\Payment\Gateway\Http\Client;
use Magento\Payment\Gateway\Http\ClientInterface;


class ZcreditTransactionVault implements ClientInterface
{
    protected $encryptor;

    protected $appState;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->encryptor = $encryptor;

        $this->appState = $context->getAppState();
    }

    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $response = null;

        $request = $transferObject->getBody();

        try {

            $soapClient = new \Zcredit\Payment\Gateway\Http\Client\lib\Soap\ZcreditClient(\Zcredit\Payment\Gateway\Config\Config::PAYMENT_URI,'wsdl');

            $soapClient->soap_defencoding = 'UTF-8';

            $soapClient->decode_utf8  = false;

            $response = $soapClient->call('CommitFullTransaction', $request);

        } catch(\Exception $e) {
           $response['error'] = $e->getMessage();
        }

        return $response;


    }
}