<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CompanyBusinessUnitTransfer;
use Spryker\Zed\CompanyBusinessUnit\Business\CompanyBusinessUnitFacadeInterface;

class PunchoutCatalogToCompanyBusinessUnitFacadeBridge implements PunchoutCatalogToCompanyBusinessUnitFacadeInterface
{
    /**
     * @var \Spryker\Zed\CompanyBusinessUnit\Business\CompanyBusinessUnitFacadeInterface
     */
    protected $companyBusinessUnitFacade;

    /**
     * @param \Spryker\Zed\CompanyBusinessUnit\Business\CompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade
     */
    public function __construct(CompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade)
    {
        $this->companyBusinessUnitFacade = $companyBusinessUnitFacade;
    }

    /**
     * @param int $idCompanyBusinessUnit
     *
     * @return \Generated\Shared\Transfer\CompanyBusinessUnitTransfer|null
     */
    public function findCompanyBusinessUnitById(int $idCompanyBusinessUnit): ?CompanyBusinessUnitTransfer
    {
        return $this->companyBusinessUnitFacade->findCompanyBusinessUnitById($idCompanyBusinessUnit);
    }
}
