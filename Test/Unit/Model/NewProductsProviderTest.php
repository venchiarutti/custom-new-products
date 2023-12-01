<?php
declare(strict_types=1);

namespace Venchiarutti\CustomNewProducts\Test\Unit\Model;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Venchiarutti\CustomNewProducts\Model\NewProductsProvider;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;

/**
 * @coversDefaultClass \Venchiarutti\CustomNewProducts\Model\NewProductsProvider
 */
class NewProductsProviderTest extends TestCase
{
    private MockObject|FilterBuilder $filterBuilderMock;
    private MockObject|FilterGroupBuilder $filterGroupBuilderMock;
    private MockObject|SortOrderBuilder $sortOrderBuilderMock;
    private MockObject|SearchCriteriaBuilder $searchCriteriaBuilderMock;
    private MockObject|ProductRepositoryInterface $productRepositoryMock;
    private MockObject|ImageHelper $imageHelperMock;
    private MockObject|ProductInterface $productMock;
    private NewProductsProvider $testSubject;

    /**
     * @covers ::__construct
     */
    public function testCanCreate(): void
    {
        $this->assertInstanceOf(NewProductsProvider::class, $this->testSubject);
    }

    /**
     * @covers ::getNewProducts
     * @covers ::getSearchCriteria
     * @covers ::mapProductData
     */
    public function testGetNewProducts(): void
    {
        $quantity = 2;

        $searchCriteriaMock = $this->prepareGetSearchCriteriaMethods($quantity);

        $productSearchResultsMock = $this->createMock(ProductSearchResultsInterface::class);

        $this->productRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($productSearchResultsMock);

        $productSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->productMock, $this->productMock]);

        $this->prepareMapProductDataMethods();

        $expected = [
            [
                'name' => 'name',
                'price' => '100,00',
                'image' => 'url'
            ],
            [
                'name' => 'name',
                'price' => '100,00',
                'image' => 'url'
            ]
        ];

        $actual = $this->testSubject->getNewProducts($quantity);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Set up mocks and subject for tests
     */
    protected function setUp(): void
    {
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);
        $this->filterGroupBuilderMock = $this->createMock(FilterGroupBuilder::class);
        $this->sortOrderBuilderMock = $this->createMock(SortOrderBuilder::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->imageHelperMock = $this->createMock(ImageHelper::class);

        $this->testSubject = new NewProductsProvider(
            $this->filterBuilderMock,
            $this->filterGroupBuilderMock,
            $this->sortOrderBuilderMock,
            $this->searchCriteriaBuilderMock,
            $this->productRepositoryMock,
            $this->imageHelperMock
        );

        $this->productMock = $this->createMock(ProductInterface::class);
    }

    /**
     * Prepare methods for getSearchCriteria private function
     *
     * @param int $quantity
     * @return MockObject|SearchCriteria
     */
    protected function prepareGetSearchCriteriaMethods(int $quantity): MockObject|SearchCriteria
    {
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with('created_at')
            ->willReturnSelf();

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setDescendingDirection')
            ->willReturnSelf();

        $sortOrderMock = $this->createMock(SortOrder::class);

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);

        $this->filterBuilderMock->expects($this->exactly(2))
            ->method('setField')
            ->withConsecutive(['status'], ['visibility'])
            ->willReturnSelf();

        $this->filterBuilderMock->expects($this->exactly(2))
            ->method('setConditionType')
            ->with('eq')
            ->willReturnSelf();

        $this->filterBuilderMock->expects($this->exactly(2))
            ->method('setValue')
            ->withConsecutive([Status::STATUS_ENABLED], [Visibility::VISIBILITY_BOTH])
            ->willReturnSelf();

        $filterMock = $this->createMock(Filter::class);

        $this->filterBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($filterMock);

        $filterGroupMock = $this->createMock(FilterGroup::class);

        $this->filterGroupBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive([$filterMock], [$filterMock])
            ->willReturnSelf();

        $this->filterGroupBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($filterGroupMock);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setFilterGroups')
            ->with([$filterGroupMock, $filterGroupMock])
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setSortOrders')
            ->with([$sortOrderMock])
            ->willReturnSelf();

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with($quantity)
            ->willReturnSelf();

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        return $searchCriteriaMock;
    }

    /**
     * Prepare methods for mapProductData private function
     */
    private function prepareMapProductDataMethods(): void
    {
        $this->productMock->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('name');

        $this->productMock->expects($this->exactly(2))
            ->method('getPrice')
            ->willReturn('100');

        $imageMock = $this->createMock(Image::class);

        $this->imageHelperMock->expects($this->exactly(2))
            ->method('init')
            ->with($this->productMock, 'product_thumbnail_image')
            ->willReturn($imageMock);

        $imageMock->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturn('url');
    }
}
