<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beckin\PackageProduct\Model\Product\Type\Package;

/**
 * Package product type implementation for backend
 */
class Backend extends \Beckin\PackageProduct\Model\Product\Type\Package
{
    /**
     * No filters required in backend
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return \Beckin\PackageProduct\Model\Product\Type\Package
     */
    public function setSaleableStatus($product)
    {
        return $this;
    }
}
