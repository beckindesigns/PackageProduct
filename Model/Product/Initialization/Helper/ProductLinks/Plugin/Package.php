<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beckin\PackageProduct\Model\Product\Initialization\Helper\ProductLinks\Plugin;

use Magento\Catalog\Api\Data\ProductLinkExtensionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Beckin\PackageProduct\Model\Product\Type\Package as TypePackage;

/**
 * Class Package
 */
class Package
{
    /**
     * String name for link type
     */
    const TYPE_NAME = 'associated';

    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $productLinkFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductLinkExtensionFactory
     */
    protected $productLinkExtensionFactory;

    /**
     * Init
     *
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkExtensionFactory $productLinkExtensionFactory
     */
    public function __construct(
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductRepositoryInterface $productRepository,
        ProductLinkExtensionFactory $productLinkExtensionFactory
    ) {
        $this->productLinkFactory = $productLinkFactory;
        $this->productRepository = $productRepository;
        $this->productLinkExtensionFactory = $productLinkExtensionFactory;
    }

    /**
     * Initialize Package product links
     *
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param array $links
     *
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function beforeInitializeLinks(
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject,
        \Magento\Catalog\Model\Product $product,
        array $links
    ) {
        if ($product->getTypeId() === TypePackage::TYPE_CODE && !$product->getPackageReadonly()) {
            $links = (isset($links[self::TYPE_NAME])) ? $links[self::TYPE_NAME] : $product->getPackageLinkData();
            if (!is_array($links)) {
                $links = [];
            }
            if ($product->getPackageLinkData()) {
                $links = array_merge($links, $product->getPackageLinkData());
            }
            $newLinks = [];
            $existingLinks = $product->getProductLinks();

            foreach ($links as $linkRaw) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                if (!isset($linkRaw['id'])) {
                    continue;
                }
                $productId = $linkRaw['id'];
                if (!isset($linkRaw['qty'])) {
                    $linkRaw['qty'] = 0;
                }
                if (!isset($linkRaw['assignedprice'])) {
                    $linkRaw['assignedprice'] = 0;
                }
                $linkedProduct = $this->productRepository->getById($productId);

                $productLink->setSku($product->getSku())
                    ->setLinkType(self::TYPE_NAME)
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($linkRaw['position'])
                if (isset($linkRaw['custom_attributes'])) {
                    $productLinkExtension = $productLink->getExtensionAttributes();
                    if ($productLinkExtension === null) {
                        $productLinkExtension = $this->productLinkExtensionFactory->create();
                    }
                    foreach ($linkRaw['custom_attributes'] as $option) {
                        $name = $option['attribute_code'];
                        $value = $option['value'];
                        $setterName = 'set' . ucfirst($name);
                        // Check if setter exists
                        if (method_exists($productLinkExtension, $setterName)) {
                            call_user_func([$productLinkExtension, $setterName], $value);
                        }
                    }
                    $productLink->setExtensionAttributes($productLinkExtension);
                }
                $newLinks[] = $productLink;
            }

            $existingLinks = $this->removeUnExistingLinks($existingLinks, $newLinks);
            $product->setProductLinks(array_merge($existingLinks, $newLinks));
        }
    }

    /**
     * Removes unexisting links
     *
     * @param array $existingLinks
     * @param array $newLinks
     * @return array
     */
    private function removeUnExistingLinks($existingLinks, $newLinks)
    {
        $result = [];
        foreach ($existingLinks as $key => $link) {
            $result[$key] = $link;
            if ($link->getLinkType() == self::TYPE_NAME) {
                $exists = false;
                foreach ($newLinks as $newLink) {
                    if ($link->getLinkedProductSku() == $newLink->getLinkedProductSku()) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }
}
