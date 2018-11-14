<?php
/**
 * @Author        : PhongPhan
 * @Email         : <phongphan.mage@gmail.com>
 * @Github        : https://github.com/phongphanmage
 * @Date: 2018/10/15
 */

namespace Zcredit\Payment\Gateway\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const PAYMENT_URI = 'https://pci.zcredit.co.il/ZCreditWS.asmx?wsdl';

    const PAYMENT_CODE = 'zcredit_cc';

    const VAULT_CODE = 'zcredit_cc_vault';

    const KEY_ACTIVE = 'active';

    const KEY_TERMINAL_NUMBER = 'terminal_number';

    const KEY_PASSWORD = 'password_zcredit';

    const KEY_CC_TYPES = 'cctypes';
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    private $encryptor;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN,
        Json $serializer = null
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        $this->setMethodCode(self::PAYMENT_CODE);
        $this->encryptor  =$encryptor;

    }

    /**
     * Gets Payment configuration status.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * Gets Terminal Number.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTerminalNumber($storeId = null)
    {
        $this->setMethodCode(self::PAYMENT_CODE);
        return $this->getValue(self::KEY_TERMINAL_NUMBER, $storeId);
    }

    /**
     * Gets Zcredit Password.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getPassword($storeId = null)
    {
        $this->setMethodCode(self::PAYMENT_CODE);
        return trim($this->getValue(self::KEY_PASSWORD, $storeId));
    }


    /**
     * Retrieve available credit card types
     *
     * @return array
     */
    public function getAvailableCardTypes()
    {
        $ccTypes = $this->getValue(self::KEY_CC_TYPES);

        return !empty($ccTypes) ? explode(',', $ccTypes) : [];
    }

}