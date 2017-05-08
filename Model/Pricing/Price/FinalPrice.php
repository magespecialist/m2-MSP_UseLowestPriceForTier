<?php
/**
 *  MageSpecialist
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Open Software License (OSL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/osl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to info@magespecialist.it so we can send you a copy immediately.
 *
 *  @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\UseLowestPriceForTier\Model\Pricing\Price;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FinalPrice extends \Magento\GroupedProduct\Pricing\Price\FinalPrice
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $_stockItem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        $quantity,
        \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_stockItem = $stockItem;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
    }

    /**
     * Returns product with minimal price based on admin config
     *
     * @return Product
     */
    public function getMinProduct()
    {
        if (null === $this->minProduct) {
            $useLowest = $this->_scopeConfig->getValue('catalog/price/use_lowest_price_for_tier');
            if ($useLowest) {
                $products = $this->product->getTypeInstance()->getAssociatedProducts($this->product);
                $minPrice = null;
                foreach ($products as $item) {
                    /* @var $product \Magento\Catalog\Model\Product */
                    $product = clone $item;
                    $productStockItem = $this->_stockItem->setProduct($product);
                    $maxQty = $productStockItem->getMaxSaleQty() ?: 9999;
                    $product->setQty($maxQty);
                    $price = $product->getPriceInfo()
                        ->getPrice(FinalPrice::PRICE_CODE)
                        ->getValue();
                    if (($price !== false) && ($price <= ($minPrice === null ? $price : $minPrice))) {
                        $this->minProduct = $product;
                        $minPrice = $price;
                    }
                }
            } else {
                parent::getMinProduct();
            }
        }
        return $this->minProduct;
    }
}
