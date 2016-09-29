<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beckin\PackageProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as CustomOptionsModifier;

/**
 * Data provider that customizes Customizable Options for Package product
 */
class CustomOptions extends AbstractModifier
{
    const PRODUCT_TYPE_PACKAGE = 'package';
    const PACKAGE_CUSTOM_OPTIONS_NAME = 'custom_options';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(LocatorInterface $locator, ArrayManager $arrayManager)
    {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();

        if ($product->getTypeId() === static::PRODUCT_TYPE_PACKAGE) {
            $data = $this->arrayManager->remove(
                $this->arrayManager->findPath(CustomOptionsModifier::FIELD_ENABLE, $data),
                $data
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === static::PRODUCT_TYPE_PACKAGE) {
            $meta = $this->arrayManager->remove(static::PACKAGE_CUSTOM_OPTIONS_NAME, $meta);
        }

        return $meta;
    }
}
