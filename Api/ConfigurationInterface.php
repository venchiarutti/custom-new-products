<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\Api;

/**
 * Interface to provide configuration data for Custom New Products module
 */
interface ConfigurationInterface
{
    /** @var string */
    public const SECTION = 'venchiarutti';

    /** @var string */
    public const GROUP = 'custom_new_products';

    /** @var string */
    public const MAX_PRODUCTS_QTY_XML_PATH = 'max_products_qty';

    /** @var string */
    public const ENABLED_XML_PATH = 'enabled';

    /**
     * Returns if Custom New Products module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns configured max quantity of products to be displayed in frontend
     *
     * @return string
     */
    public function getMaxProductsQty(): string;
}
