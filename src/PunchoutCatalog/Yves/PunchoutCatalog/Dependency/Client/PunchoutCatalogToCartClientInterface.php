<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client;

use Generated\Shared\Transfer\QuoteResponseTransfer;

interface PunchoutCatalogToCartClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function validateQuote(): QuoteResponseTransfer;
}
