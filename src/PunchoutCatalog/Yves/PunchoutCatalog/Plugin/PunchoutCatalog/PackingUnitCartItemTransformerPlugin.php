<?php


namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\PunchoutCatalog;

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
     * @param \Generated\Shared\Transfer\ItemTransfer[] $cartItems
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function transformCartItems(array $cartItems, QuoteTransfer $quoteTransfer): array
    {
        $transformedCartItems = [];

        foreach ($cartItems as $cartItem) {
            $transformedCartItems[] = $cartItem;
        }

        return $transformedCartItems;
    }
}
