<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Generated\Shared\Transfer\CompanyUserTransfer;

class PunchoutCatalogToCompanyUserFacadeBridge implements PunchoutCatalogToCompanyUserFacadeInterface
{
    /**
     * @var \Spryker\Zed\CompanyUser\Business\CompanyUserFacadeInterface
     */
    protected $companyUserFacade;
    
    /**
     * @param \Spryker\Zed\CompanyUser\Business\CompanyUserFacade $companyUserFacade
     */
    public function __construct($companyUserFacade)
    {
        $this->companyUserFacade = $companyUserFacade;
    }
    
    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer
     */
    public function findCompanyBusinessUnitUser(CompanyUserTransfer $companyUserTransfer): CompanyUserTransfer
    {
        var_dump(get_class($this->companyUserFacade->getEntityManager()->getFactory()));exit;
        $this->companyUserFacade->getEntityManager()->getFactory();
    }
}
