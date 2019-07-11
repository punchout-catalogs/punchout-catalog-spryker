<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface CartTransferMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    public function mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer;
}
