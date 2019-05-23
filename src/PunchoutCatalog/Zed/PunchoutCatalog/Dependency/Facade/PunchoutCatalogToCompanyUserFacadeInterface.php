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
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer|null
     */
    public function findCompanyUserById(int $idCompanyUser): ?CompanyUserTransfer;
}
