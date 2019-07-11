<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer;

use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

interface CustomerModeStrategyInterface
{
    /**
     * @param PunchoutCatalogConnectionTransfer $connectionTransfer
     * @param PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer
     *
     * @return CustomerTransfer
     */
    public function getCustomerTransfer(
        PunchoutCatalogConnectionTransfer $connectionTransfer,
        PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer = null
    ) : CustomerTransfer;
}
