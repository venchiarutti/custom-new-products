<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\Api;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface to provide new products entities
 */
interface NewProductsProviderInterface
{
    /**
     * Get newest products by the quantity of products requested
     *
     * @param int $quantity
     * @return ProductInterface[]
     */
    public function getNewProducts(int $quantity): array;
}
