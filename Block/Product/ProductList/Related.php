<?php

namespace MSP\UseLowestPriceForTier\Block\Product\ProductList;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{

    /**
     * Return HTML block with price and display minimal price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        return $this->getProductPriceHtml(
            $product,
            \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
            \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
            [
                'display_minimal_price' => true,
            ]
        );
    }

}
