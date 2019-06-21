<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

interface PunchoutCatalogToStoreFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\StoreTransfer[]
     */
    public function getAllStores();
}
