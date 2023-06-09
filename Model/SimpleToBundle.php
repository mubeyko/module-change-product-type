<?php
declare(strict_types=1);

namespace Mubeyko\ChangeProductType\Model;

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\ProductRepository;
use Magento\Bundle\Model\Product\Type as BundleType;
use LogicException;

class SimpleToBundle
{
    public const SIMPLE_TYPE_CODE = "simple";
    public const PRICE_RANGE = 0;
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     */

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Change product type from Simple to Bundle
     *
     * @param string $sku
     * @return void
     * @throws \Exception
     */
    public function changeTypeToBundle(string $sku): void
    {
        try {
            $product = $this->productRepository->get($sku);
            if ($product->getTypeId() !== self::SIMPLE_TYPE_CODE) {
                throw new LogicException('Product with entered SKU has a not Simple type');
            }
            $product->setTypeId(BundleType::TYPE_CODE);
            $product->setPriceType(Price::PRICE_TYPE_FIXED);
            $product->setPriceView(self::PRICE_RANGE);
            $product->setQty(0);
            $this->productRepository->save($product);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
