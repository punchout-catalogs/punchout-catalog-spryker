<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\EntryPoint;

use Generated\Shared\Transfer\PunchoutCatalogEntryPointFilterTransfer;
use Generated\Shared\Transfer\PunchoutCatalogEntryPointTransfer;

interface RequestEntryPointReaderInterface
{
    /**
     * @param PunchoutCatalogEntryPointFilterTransfer $entryPointFilter
     *
     * @return PunchoutCatalogEntryPointTransfer[]
     */
    public function getRequestEntryPointsByBusinessUnit(PunchoutCatalogEntryPointFilterTransfer $entryPointFilter): array;
}
