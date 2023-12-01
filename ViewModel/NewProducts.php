<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\ViewModel;

use Venchiarutti\CustomNewProducts\Api\ConfigurationInterface;
use Venchiarutti\CustomNewProducts\Api\NewProductsProviderInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * View model for new products block
 */
class NewProducts implements ArgumentInterface
{
    /**
     * @param ConfigurationInterface $configuration
     * @param NewProductsProviderInterface $newProductsProvider
     */
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly NewProductsProviderInterface $newProductsProvider
    ) {
    }

    /**
     * Returns if should display new products block
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configuration->isEnabled();
    }

    /**
     * Returns new products entities
     *
     * @return array
     */
    public function getNewProducts(): array
    {
        return $this->newProductsProvider->getNewProducts((int) $this->configuration->getMaxProductsQty());
    }
}
