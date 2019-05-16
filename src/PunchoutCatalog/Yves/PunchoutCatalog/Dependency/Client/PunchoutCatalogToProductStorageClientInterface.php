<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client;

use Generated\Shared\Transfer\ProductConcreteTransfer;

interface PunchoutCatalogToProductStorageClientInterface
{
    /**
     * @param $idProductAbstract
     * @param $localeName
     * @return mixed
     */
    public function getProductAbstractStorageData($idProductAbstract, $localeName): array;

}