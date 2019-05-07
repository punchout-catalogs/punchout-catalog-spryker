<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client;

use Generated\Shared\Transfer\MoneyTransfer;

interface PunchoutCatalogPageToMoneyClientInterface
{
    /**
     * @param int $amount
     * @param string|null $isoCode
     *
     * @return \Generated\Shared\Transfer\MoneyTransfer
     */
    public function fromInteger(int $amount, ?string $isoCode): MoneyTransfer;
}
