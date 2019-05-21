<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class CartTransferMapper implements CartTransferMapperInterface
{
    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartTransferMapperPluginInterface[] | CartTransferMapperDefaultPlugin[]
     */
    protected $cartTransferMapperPlugins;

    /**
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartTransferMapperPluginInterface[]
     */
    public function __construct(array $cartTransferMapperPlugins)
    {
        $this->cartTransferMapperPlugins = $cartTransferMapperPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    public function mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        foreach ($this->cartTransferMapperPlugins as $plugin) {
            $cartRequestTransfer = $plugin->mapQuoteTransferToPunchoutCatalogCartRequestTransfer($quoteTransfer, $cartRequestTransfer);
        }

        return $cartRequestTransfer;
    }
}
