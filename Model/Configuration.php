<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Venchiarutti\CustomNewProducts\Api\ConfigurationInterface;

/**
 * Class to provide configuration data for Custom New Products module
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Returns if Custom New Products module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::SECTION . '/' . self::GROUP . '/' . self::ENABLED_XML_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns configured max quantity of products to be displayed in frontend
     *
     * @return string
     */
    public function getMaxProductsQty(): string
    {
        return $this->scopeConfig->getValue(
            self::SECTION . '/' . self::GROUP . '/' . self::MAX_PRODUCTS_QTY_XML_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }
}
