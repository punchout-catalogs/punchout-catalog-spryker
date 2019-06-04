<?php

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;

interface CartItemTransformerPluginInterface
{
    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer[] $cartItems
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function transformCartItems(array $cartItems, QuoteTransfer $quoteTransfer): array;
}
