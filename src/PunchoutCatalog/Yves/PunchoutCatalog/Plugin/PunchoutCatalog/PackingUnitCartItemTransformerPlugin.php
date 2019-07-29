<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\PunchoutCatalog;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class PackingUnitCartItemTransformerPlugin extends AbstractPlugin implements CartItemTransformerPluginInterface
{
    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientInterface
     */
    protected $productStorageClient;

    /**
     * PackingUnitCartItemTransformerPlugin constructor.
     * @throws \Spryker\Shared\Kernel\Locale\LocaleNotFoundException
     */
    public function __construct()
    {
        $this->currentLocale = $this->getFactory()->getStore()->getCurrentLocale();
        $this->productStorageClient = $this->getFactory()->getProductStorageClient();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $cartItems
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function transformCartItems(array $cartItems, QuoteTransfer $quoteTransfer): array
    {
        $transformedCartItems = [];

        foreach ($cartItems as $cartItem) {
            $amountSalesUnit = $cartItem->getAmountSalesUnit();
            $amountLeadProduct = $cartItem->getAmountLeadProduct();
            if ($amountSalesUnit && $amountLeadProduct) {
                $data[] = $cartItem;
                $childItem = new ItemTransfer();
                $name = $cartItem->getName();
                $productData = $this->productStorageClient->findProductAbstractStorageData($cartItem->getIdProductAbstract(), $this->currentLocale);
                if (!empty($productData['name'])) {
                    $name = $productData['name'];
                }
                $childItem->setName($name);
                $childItem->setIdProductAbstract($amountLeadProduct->getIdProductAbstract());
                $childItem->setSku($amountLeadProduct->getProduct()->getSku());
                $childItem->setUnitPrice($cartItem->getUnitPrice());
                $childItem->setSumPrice($cartItem->getSumPrice());
                $childItem->setQuantity($cartItem->getAmount() / $cartItem->getQuantity());
                $childItem->setUnitPriceToPayAggregation($cartItem->getUnitPriceToPayAggregation());
                $childItem->setSumPriceToPayAggregation($cartItem->getSumPriceToPayAggregation());

                $concreteAttributes = $cartItem->getConcreteAttributes();
                if (!empty($concreteAttributes['packaging_unit'])) {
                    $cartItem->setName($concreteAttributes['packaging_unit']);
                }
                
                $cartItem->setChildBundleItems(new ArrayObject([$childItem]));
                $transformedCartItems[] = $cartItem;
            } else {
                $transformedCartItems[] = $cartItem;
            }
        }

        return $transformedCartItems;
    }
}
