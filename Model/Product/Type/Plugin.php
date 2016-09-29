<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Beckin\PackageProduct\Model\Product\Type;

use Magento\Framework\Module\Manager;

class Plugin
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param Manager $moduleManager
     */
    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Remove grouped product from list of visible product types
     *
     * @param \Magento\Catalog\Model\Product\Type $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOptionArray(\Magento\Catalog\Model\Product\Type $subject, array $result)
    {
        if (!$this->moduleManager->isOutputEnabled('Beckin_PackageProduct')) {
            unset($result[Package::TYPE_CODE]);
        }
        return $result;
    }
}
