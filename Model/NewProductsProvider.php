<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Venchiarutti\CustomNewProducts\Api\NewProductsProviderInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class to provide new products entities
 */
class NewProductsProvider implements NewProductsProviderInterface
{
    /**
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        private readonly FilterBuilder $filterBuilder,
        private readonly FilterGroupBuilder $filterGroupBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ImageHelper $imageHelper
    ) {
    }

    /**
     * Get newest products by the quantity of products requested
     *
     * @param int $quantity
     * @return ProductInterface[]
     */
    public function getNewProducts(int $quantity): array
    {
        $products = $this->productRepository->getList(
            $this->getSearchCriteria($quantity)
        )->getItems();

        return array_map([$this, 'mapProductData'], $products);
    }

    /**
     * Map essential product data to be returned
     *
     * @param ProductInterface $product
     * @return array
     */
    private function mapProductData(ProductInterface $product): array
    {
        return [
            'name' => $product->getName(),
            'price' => number_format((float) $product->getPrice(), 2, ',', ''),
            'image' => $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl()
        ];
    }

    /**
     * Returns search criteria for new products
     *
     * @param int $quantity
     * @return SearchCriteria
     */
    private function getSearchCriteria(int $quantity): SearchCriteria
    {
        $sortOrder = $this->sortOrderBuilder->setField('created_at')
            ->setDescendingDirection()
            ->create();

        $filterGroups = [];

        $filterGroups[] = $this->filterGroupBuilder->addFilter(
            $this->filterBuilder->setField('status')
                ->setConditionType('eq')
                ->setValue(Status::STATUS_ENABLED)
                ->create()
        )->create();

        $filterGroups[] = $this->filterGroupBuilder->addFilter(
            $this->filterBuilder->setField('visibility')
                ->setConditionType('eq')
                ->setValue(Visibility::VISIBILITY_BOTH)
                ->create()
        )->create();

        return $this->searchCriteriaBuilder->setFilterGroups($filterGroups)
            ->setSortOrders([$sortOrder])
            ->setPageSize($quantity)
            ->create();
    }
}
