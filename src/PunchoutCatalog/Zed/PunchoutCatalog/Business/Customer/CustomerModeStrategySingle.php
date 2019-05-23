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
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

class CustomerModeStrategySingle implements CustomerModeStrategyInterface
{
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
     * @param PunchoutCatalogConnectionTransfer $connectionTransfer
     * @param PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer
     *
     * @return CustomerTransfer
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
        $connectionTransfer->getSetup()->requireFkCompanyBusinessUnit();
    
        $companyUserTransfer = (new CompanyUserTransfer())->setIdCompanyUser(11);
    
        $companyUserTransfer = $this->companyUserFacade->findCompanyUserById(
            $connectionTransfer->getSetup()->getFkCompanyUser()
        );
        
        if (null === $companyUserTransfer
            || $companyUserTransfer->getFkCompanyBusinessUnit() != $connectionTransfer->getSetup()->getFkCompanyBusinessUnit()
        ) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_USER);
        }
        
        return (new CustomerTransfer())
            ->setCompanyUserTransfer($companyUserTransfer);;
    }
}
