<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CompanyUserTransfer;

interface PunchoutCatalogToCompanyUserFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer
     */
    public function findCompanyBusinessUnitUser(CompanyUserTransfer $companyUserTransfer): CompanyUserTransfer;
}
