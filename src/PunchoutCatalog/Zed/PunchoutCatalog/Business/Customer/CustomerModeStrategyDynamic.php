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
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

class CustomerModeStrategyDynamic implements CustomerModeStrategyInterface
{

    
    /**
     * @param int $companyBusinessUnitId
     *
     * @return \Generated\Shared\Transfer\CompanyBusinessUnitTransfer|null
     * @throws AuthenticateException
     */
    protected function getCompanyBusinessUnit(int $companyBusinessUnitId)
    {
        $businessUnitTransfer = $this->companyBusinessUnitFacade->findCompanyBusinessUnitById($companyBusinessUnitId);
        
        if (!$businessUnitTransfer) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_BUSINESS_UNIT);
        }
        
        return $businessUnitTransfer;
    }
    
    /**
     * @param PunchoutCatalogConnectionTransfer $connectionTransfer
     * @param PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer
     *
     * @return CustomerTransfer
     */
    public function getCustomerTransfer(
        PunchoutCatalogConnectionTransfer $connectionTransfer,
        PunchoutCatalogDocumentCustomerTransfer $documentCustomerTransfer = null
    ) : CustomerTransfer
    {
        if (null === $customerTransfer) {
            $customerTransfer = new CustomerTransfer();
        }
    
        $customerTransfer->setIsGuest(false);
    
        $customerTransfer->fromArray($documentCustomerTransfer->toArray(), true);
        
        $businessUnit = $this->getCompanyBusinessUnit($documentCustomerTransfer->getFkCompanyBusinessUnit());

        $customerTransfer->setCompanyUserTransfer(
            (new CompanyUserTransfer())
                 ->setFkCustomer($customerTransfer->getIdCustomer())
                 ->setFkCompany($businessUnit->getFkCompany())
                 ->setFkCompanyBusinessUnit($businessUnit->getIdCompanyBusinessUnit())
                 ->setIsActive(true)
        );
        
        return $customerTransfer;
    }
}
