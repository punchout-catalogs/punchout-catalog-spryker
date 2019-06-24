<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Generated\Shared\Transfer\StoreTransfer;

class PunchoutCatalogToStoreFacadeBridge implements PunchoutCatalogToStoreFacadeInterface
{
    /**
     * @var StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param StoreFacadeInterface $storeFacade
     */
    public function __construct(StoreFacadeInterface $storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }
    
    /**
     * @return \Generated\Shared\Transfer\StoreTransfer[]
     */
    public function getAllStores()
    {
        return $this->storeFacade->getAllStores();
    }

    /**
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getCurrentStore(): StoreTransfer
    {
        return $this->storeFacade->getCurrentStore();
    }
}
