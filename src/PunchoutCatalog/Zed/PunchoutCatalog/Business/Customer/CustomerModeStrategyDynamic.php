<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

class CustomerModeStrategyDynamic implements CustomerModeStrategyInterface
{
    /**
     * @uses \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategySingle::getCustomerTransfer
     */
    protected const ERROR_MISSING_COMPANY_USER = 'punchout-catalog.error.missing-company-user';

    protected const ERROR_TOO_MANY_COMPANY_USERS = 'punchout-catalog.error.too-many-company-users';

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
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface
     */
    protected $companyBusinessUnitFacade;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface $punchoutCatalogRepository
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface $customerFacade
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade
     */
    public function __construct(
        PunchoutCatalogRepositoryInterface $punchoutCatalogRepository,
        PunchoutCatalogToCompanyUserFacadeInterface $companyUserFacade,
        PunchoutCatalogToCustomerFacadeInterface $customerFacade,
        PunchoutCatalogToCompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade
    )
    {
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;
        $this->companyUserFacade = $companyUserFacade;
        $this->customerFacade = $customerFacade;
        $this->companyBusinessUnitFacade = $companyBusinessUnitFacade;
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
    ): CustomerTransfer
    {
        $connectionTransfer->requireSetup();
        $connectionTransfer->getSetup()->requireFkCompanyBusinessUnit();

        if (null === $documentCustomerTransfer) {
            throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
        }
        $documentCustomerTransfer->requireEmail();

        $currentBusinessUnitId = $connectionTransfer->getSetup()->getFkCompanyBusinessUnit();
        $currentBusinessUnit = $this->companyBusinessUnitFacade->findCompanyBusinessUnitById($currentBusinessUnitId);

        //Try to find user by email
        $customerId = $this->punchoutCatalogRepository->findCustomerIdByEmail(
            $documentCustomerTransfer->getEmail()
        );

        //If not found -- create new user
        if ($customerId === null) {
            try {
                $documentCustomerTransfer->requireFirstName();
                $documentCustomerTransfer->requireLastName();
            } catch (RequiredTransferPropertyException $e) {
                throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
            }
            
            $customerTransfer = new CustomerTransfer();
            $customerTransfer->setEmail($documentCustomerTransfer->getEmail());
            $customerTransfer->setFirstName($documentCustomerTransfer->getFirstName());
            $customerTransfer->setLastName($documentCustomerTransfer->getLastName());
            
            $customerResponseTransfer = $this->customerFacade->addCustomer($customerTransfer);

            if (!$customerResponseTransfer->getIsSuccess()) {
                throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
            }

            //And create new connection to company
            $companyUserTransfer = new CompanyUserTransfer();
            $companyUserTransfer->setCustomer($customerTransfer);
            $companyUserTransfer->setFkCustomer($customerTransfer->getIdCustomer());
            $companyUserTransfer->setCompany($currentBusinessUnit->getCompany());
            $companyUserTransfer->setFkCompany($currentBusinessUnit->getFkCompany());
            $companyUserTransfer->setCompanyBusinessUnit($currentBusinessUnit);
            $companyUserTransfer->setFkCompanyBusinessUnit($currentBusinessUnitId);

            $companyUserResponseTransfer = $this->companyUserFacade->create($companyUserTransfer);

            if (!$companyUserResponseTransfer->getIsSuccessful()) {
                throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
            }
        } else {
            $customerTransfer = $this->customerFacade->findCustomerById((new CustomerTransfer())->setIdCustomer($customerId));

            //Check if customer connected with current company
            //Omit case if user assign to another BU

            $companyUserIds = $this->punchoutCatalogRepository->findIdCompanyUsersInCompany(
                $customerTransfer->getIdCustomer(), $currentBusinessUnit->getFkCompany());

            if (count($companyUserIds) > 1) {
                throw new AuthenticateException(self::ERROR_TOO_MANY_COMPANY_USERS);
            }

            if (empty($companyUserIds)) {
                throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
            }

            $companyUserId = $companyUserIds[0];

            $companyUserTransfer = $this->companyUserFacade->getCompanyUserById($companyUserId);
        }

        if ($companyUserTransfer === null) {
            throw new AuthenticateException(self::ERROR_MISSING_COMPANY_USER);
        }

        return (new CustomerTransfer())
            ->setCompanyUserTransfer($companyUserTransfer);
    }
}
