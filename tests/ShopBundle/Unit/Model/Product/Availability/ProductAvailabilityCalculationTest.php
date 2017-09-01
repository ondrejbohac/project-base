<?php

namespace Tests\ShopBundle\Unit\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\Model\Product\Availability\Availability;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;
use Tests\ShopBundle\Test\FunctionalTestCase;

class ProductAvailabilityCalculationTest extends FunctionalTestCase
{
    /**
     * @dataProvider getTestCalculateAvailabilityData
     */
    public function testCalculateAvailability(
        $usingStock,
        $stockQuantity,
        $outOfStockAction,
        Availability $availability = null,
        Availability $outOfStockAvailability = null,
        Availability $defaultInStockAvailability = null,
        Availability $expectedCalculatedAvailability = null
    ) {
        $productData = new ProductData();
        $productData->usingStock = $usingStock;
        $productData->stockQuantity = $stockQuantity;
        $productData->availability = $availability;
        $productData->outOfStockAction = $outOfStockAction;
        $productData->outOfStockAvailability = $outOfStockAvailability;

        $product = Product::create($productData);

        $availabilityFacadeMock = $this->getMockBuilder(AvailabilityFacade::class)
            ->setMethods(['getDefaultInStockAvailability'])
            ->disableOriginalConstructor()
            ->getMock();
        $availabilityFacadeMock->expects($this->any())->method('getDefaultInStockAvailability')
            ->will($this->returnValue($defaultInStockAvailability));

        $productSellingDeniedRecalculatorMock = $this->createMock(ProductSellingDeniedRecalculator::class);
        $productVisibilityFacadeMock = $this->createMock(ProductVisibilityFacade::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $productRepositoryMock = $this->createMock(ProductRepository::class);

        $productAvailabilityCalculation = new ProductAvailabilityCalculation(
            $availabilityFacadeMock,
            $productSellingDeniedRecalculatorMock,
            $productVisibilityFacadeMock,
            $entityManagerMock,
            $productRepositoryMock
        );

        $calculatedAvailability = $productAvailabilityCalculation->calculateAvailability($product);

        $this->assertSame($expectedCalculatedAvailability, $calculatedAvailability);
    }

    public function getTestCalculateAvailabilityData()
    {
        return [
            [
                'usingStock' => false,
                'stockQuantity' => null,
                'outOfStockAction' => null,
                'availability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'outOfStockAvailability' => null,
                'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
            ],
            [
                'usingStock' => true,
                'stockQuantity' => null,
                'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_HIDE,
                'availability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'outOfStockAvailability' => null,
                'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
            ],
            [
                'usingStock' => true,
                'stockQuantity' => 5,
                'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
                'availability' => null,
                'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
                'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
            ],
            [
                'usingStock' => true,
                'stockQuantity' => 0,
                'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
                'availability' => null,
                'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
                'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
            ],
            [
                'usingStock' => true,
                'stockQuantity' => -1,
                'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
                'availability' => null,
                'outOfStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
                'defaultInStockAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK),
                'calculatedAvailability' => $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK),
            ],
        ];
    }

    public function testCalculateAvailabilityMainVariant()
    {
        $productData = new ProductData();

        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        $variant1 = Product::create($productData);

        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);
        $variant2 = Product::create($productData);

        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $variant3 = Product::create($productData);

        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_PREPARING);
        $variant4 = Product::create($productData);

        $variants = [$variant1, $variant2, $variant3, $variant4];
        $mainVariant = Product::createMainVariant(new ProductData(), $variants);

        $availabilityFacadeMock = $this->createMock(AvailabilityFacade::class);
        $productSellingDeniedRecalculatorMock = $this->createMock(ProductSellingDeniedRecalculator::class);
        $productVisibilityFacadeMock = $this->createMock(ProductVisibilityFacade::class);
        $entityManagerMock = $this->createMock(EntityManager::class);

        $productRepositoryMock = $this->createMock(ProductRepository::class);
        $productRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getAtLeastSomewhereSellableVariantsByMainVariant')
            ->with($this->equalTo($mainVariant))
            ->willReturn($variants);

        $productAvailabilityCalculation = new ProductAvailabilityCalculation(
            $availabilityFacadeMock,
            $productSellingDeniedRecalculatorMock,
            $productVisibilityFacadeMock,
            $entityManagerMock,
            $productRepositoryMock
        );

        $variant1->setCalculatedAvailability($productAvailabilityCalculation->calculateAvailability($variant1));
        $variant2->setCalculatedAvailability($productAvailabilityCalculation->calculateAvailability($variant2));
        $variant3->setCalculatedAvailability($productAvailabilityCalculation->calculateAvailability($variant3));
        $variant4->setCalculatedAvailability($productAvailabilityCalculation->calculateAvailability($variant4));

        $mainVariantCalculatedAvailability = $productAvailabilityCalculation->calculateAvailability($mainVariant);

        $this->assertSame($variant1->getCalculatedAvailability(), $mainVariantCalculatedAvailability);
    }

    public function testCalculateAvailabilityMainVariantWithNoSellableVariants()
    {
        $productData = new ProductData();
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);
        $variant = Product::create($productData);

        $mainVariant = Product::createMainVariant(new ProductData(), [$variant]);

        $availabilityFacadeMock = $this->getMockBuilder(AvailabilityFacade::class)
            ->setMethods(['getDefaultInStockAvailability'])
            ->disableOriginalConstructor()
            ->getMock();
        $defaultInStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        $availabilityFacadeMock
            ->expects($this->any())
            ->method('getDefaultInStockAvailability')
            ->willReturn($defaultInStockAvailability);
        $productSellingDeniedRecalculatorMock = $this->createMock(ProductSellingDeniedRecalculator::class);
        $productVisibilityFacadeMock = $this->createMock(ProductVisibilityFacade::class);
        $entityManagerMock = $this->createMock(EntityManager::class);

        $productRepositoryMock = $this->createMock(ProductRepository::class);
        $productRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getAtLeastSomewhereSellableVariantsByMainVariant')
            ->with($this->equalTo($mainVariant))
            ->willReturn([]);

        $productAvailabilityCalculation = new ProductAvailabilityCalculation(
            $availabilityFacadeMock,
            $productSellingDeniedRecalculatorMock,
            $productVisibilityFacadeMock,
            $entityManagerMock,
            $productRepositoryMock
        );

        $variant->setCalculatedAvailability($productAvailabilityCalculation->calculateAvailability($variant));

        $mainVariantCalculatedAvailability = $productAvailabilityCalculation->calculateAvailability($mainVariant);

        $this->assertSame($defaultInStockAvailability, $mainVariantCalculatedAvailability);
    }
}
