<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer;

use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface;

use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

class CustomerModeStrategySingle implements CustomerModeStrategyInterface
{
    /**
     * @var PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface
     */
    protected $companyBusinessUnitFacade;
    
    /**
     * @var PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface $businessUnitFacade
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface $customerFacade
     */
    public function __construct(
        PunchoutCatalogToCompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade,
        PunchoutCatalogToCustomerFacadeInterface $customerFacade
    )
    {
        $this->companyBusinessUnitFacade = $companyBusinessUnitFacade;
        $this->customerFacade = $customerFacade;
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
    
        $customerTransfer = $this->customerFacade->findCustomerById(
            $connectionTransfer->getSetup()->getFkCompanyUser()
        );
        
        if (!$customerTransfer) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_USER);
        }
    
        
        $companyBusinessUnit = $this->companyBusinessUnitFacade->findCompanyBusinessUnitById(
            $connectionTransfer->getSetup()->getFkCompanyBusinessUnit()
        );
        
        if (!$companyBusinessUnit) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_BUSINESS_UNIT);
        }
        
        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCompanyBusinessUnit($companyBusinessUnit->getIdCompanyBusinessUnit())
            ->setFkCompany($companyBusinessUnit->getFkCompany())
            ->setFkCustomer($customerTransfer->getIdCustomer())
            //->setIdCompanyUser($customerTransfer->getCompanyUserTransfer()->getIdCompanyUser())
            ->setIdCompanyUser($customerTransfer->getIdCustomer())//@todo: review value of this field
        ;

        $customerTransfer->setCompanyUserTransfer($companyUserTransfer);
        
        return $customerTransfer;
    }
}
