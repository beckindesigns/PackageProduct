<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beckin\PackageProduct\Model\Product\CopyConstructor;

class Package implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * Retrieve collection Package link
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    protected function _getPackageLinkCollection(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\Catalog\Model\Product\Link  $links */
        $links = $product->getLinkInstance();
        $links->setLinkTypeId(\Beckin\PackageProduct\Model\ResourceModel\Product\Link::LINK_TYPE_PACKAGE);

        $collection = $links->getLinkCollection();
        $collection->setProduct($product);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    /**
     * Build product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     * @return void
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        if ($product->getTypeId() !== \Beckin\PackageProduct\Model\Product\Type\Package::TYPE_CODE) {
            //do nothing if not Package product
            return;
        }

        $data = [];
        $attributes = [];
        $link = $product->getLinkInstance();
        $link->setLinkTypeId(\Beckin\PackageProduct\Model\ResourceModel\Product\Link::LINK_TYPE_PACKAGE);
        foreach ($link->getAttributes() as $attribute) {
            if (isset($attribute['code'])) {
                $attributes[] = $attribute['code'];
            }
        }

        /** @var \Magento\Catalog\Model\Product\Link $link  */
        foreach ($this->_getPackageLinkCollection($product) as $link) {
            $data[$link->getLinkedProductId()] = $link->toArray($attributes);
        }
        $duplicate->setPackageLinkData($data);
    }
}
