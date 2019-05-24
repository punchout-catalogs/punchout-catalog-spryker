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

use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

class CustomerModeStrategyDynamic implements CustomerModeStrategyInterface
{
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface
     */
    protected $punchoutCatalogRepository;
   
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface
     */
    protected $companyUserFacade;
    
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface
     */
    protected $customerFacade;
    
    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface $punchoutCatalogRepository
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface $customerFacade
     */
    public function __construct(
        PunchoutCatalogRepositoryInterface $punchoutCatalogRepository,
        PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade,
        PunchoutCatalogToCustomerFacadeInterface $customerFacade
    )
    {
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;
        $this->companyUserFacade = $companyUserFacade;
        $this->customerFacade = $customerFacade;
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
        $connectionTransfer->getSetup()->requireFkCompanyBusinessUnit();
        
        if (null === $documentCustomerTransfer) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_USER);
        }
        $documentCustomerTransfer->requireEmail();
        $documentCustomerTransfer->setEmail('george.freeman@spryker.com');//@todo: remove
        
        $customerId = $this->punchoutCatalogRepository->findCustomerIdByEmail(
            $documentCustomerTransfer->getEmail()
        );
        
        if (null === $customerId) {
            die('CREATE NEW CUSTOMER HERE');
        } else {
            $customerTransfer = $this->customerFacade->findCustomerById($customerId);
        }
        
        var_dump($customerId);
        dd($customerTransfer);
        exit;
        
        $companyUserTransfer = $this->customerFacade->findCustomerById();
        
        dd($documentCustomerTransfer);
        $companyUserTransfer = $this->companyUserFacade->findCompanyUserById(
            $connectionTransfer->getSetup()->getFkCompanyUser()
        );
        
        if (null === $companyUserTransfer
            || $companyUserTransfer->getFkCompanyBusinessUnit() != $connectionTransfer->getSetup()->getFkCompanyBusinessUnit()
        ) {
            throw new AuthenticateException(PunchoutConnectionConstsInterface::ERROR_MISSING_COMPANY_USER);
        }
        
        return (new CustomerTransfer())
            ->setCompanyUserTransfer($companyUserTransfer);
    }
}
