<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer;

use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface;

use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;

class CustomerModeStrategySingle implements CustomerModeStrategyInterface
{
    /**
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyDynamic
     */
    protected const ERROR_MISSING_COMPANY_USER = 'punchout-catalog.error.missing-company-user';

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface
     */
    protected $companyUserFacade;
    
    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade
     */
    public function __construct(PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade)
    {
        $this->companyUserFacade = $companyUserFacade;
    }
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $connectionTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     *
     * @throws AuthenticateException
     */
    public function getCustomerTransfer(
        PunchoutCatalogConnectionTransfer $connectionTransfer,
        PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer = null
    ) : CustomerTransfer
    {
        $connectionTransfer->requireSetup();
        $connectionTransfer->getSetup()->requireFkCompanyUser();
    
        $companyUserTransfer = $this->companyUserFacade->findCompanyUserById(
            $connectionTransfer->getSetup()->getFkCompanyUser()
        );
        
        if (null === $companyUserTransfer) {
            throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
        }
        
        return (new CustomerTransfer())
            ->setCompanyUserTransfer($companyUserTransfer);
    }
}
