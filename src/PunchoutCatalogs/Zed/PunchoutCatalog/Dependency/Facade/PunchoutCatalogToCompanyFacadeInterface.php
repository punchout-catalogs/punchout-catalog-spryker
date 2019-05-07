<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CompanyTransfer;

interface PunchoutCatalogToCompanyFacadeInterface
{
    /**
     * @param string $uuidCompany
     *
     * @return \Generated\Shared\Transfer\CompanyTransfer|null
     */
    public function findCompanyByUuid(string $uuidCompany): ?CompanyTransfer;
}
