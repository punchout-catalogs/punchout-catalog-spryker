<?php

/**
 *
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class ProductBundleCartItemTransformerPlugin extends AbstractPlugin implements CartItemTransformerPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $cartItems
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function transformCartItems(array $cartItems, QuoteTransfer $quoteTransfer): array
    {
        $transformedCartItems = [];
        
        $groupedItems = $this->getGroupedItems($cartItems, $quoteTransfer);
        foreach ($groupedItems as $groupedItem) {
            if ($groupedItem instanceof ItemTransfer) {
                $transformedCartItems[] = $groupedItem;
                continue;
            }
            
            // Punchout Specific Code
            if (!empty($groupedItem['bundleItems'])) {
                foreach ($groupedItem['bundleItems'] as $bundleItem) {
                    $groupedItem['bundleProduct']->addChildBundleItems($bundleItem);
                }
            }
            // Punchout Specific Code
            
            $transformedCartItems[] = $groupedItem['bundleProduct'];
        }
        
        return $transformedCartItems;
    }
    
    /**
     * @param array $cartItems
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getGroupedItems(array $cartItems, QuoteTransfer $quoteTransfer): array
    {
        return $this->getFactory()
            ->getProductBundleClient()
            ->getGroupedBundleItems(new ArrayObject($cartItems), $quoteTransfer->getBundleItems());
    }
}
