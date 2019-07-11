<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client;

use Generated\Shared\Transfer\QuoteResponseTransfer;

class PunchoutCatalogToCartClientBridge implements PunchoutCatalogToCartClientInterface
{
    /**
     * @var \Spryker\Client\Cart\CartClientInterface
     */
    protected $cartClient;
    
    /**
     * @param \Spryker\Client\Quote\QuoteClientInterface $cartClient
     */
    public function __construct($cartClient)
    {
        $this->cartClient = $cartClient;
    }
    
    /**
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function validateQuote(): QuoteResponseTransfer
    {
        return $this->cartClient->validateQuote();
    }
}
