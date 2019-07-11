<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client;

use Generated\Shared\Transfer\MoneyTransfer;

class PunchoutCatalogToMoneyClientBridge implements PunchoutCatalogToMoneyClientInterface
{
    /**
     * @var \Spryker\Client\Money\MoneyClientInterface
     */
    protected $moneyClient;

    /**
     * @param \Spryker\Client\Money\MoneyClientInterface $moneyClient
     */
    public function __construct($moneyClient)
    {
        $this->moneyClient = $moneyClient;
    }

    /**
     * @param int $amount
     * @param string|null $isoCode
     *
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    public function fromInteger(int $amount, ?string $isoCode): MoneyTransfer
    {
        return $this->moneyClient->fromInteger($amount, $isoCode);
    }
}
