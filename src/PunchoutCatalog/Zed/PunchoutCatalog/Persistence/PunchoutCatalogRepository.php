<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogPersistenceFactory getFactory()
 */
class PunchoutCatalogRepository extends AbstractRepository implements PunchoutCatalogRepositoryInterface
{
    /**
     * @param int $connectionId
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionById(int $connectionId): ?PunchoutCatalogConnectionTransfer
    {
        $connectionEntity = $this->getFactory()
            ->createPunchoutCatalogConnectionQuery()
            ->filterByIdPunchoutCatalogConnection($connectionId)
            ->joinWithPgwPunchoutCatalogConnectionCart()
            ->joinWithPgwPunchoutCatalogConnectionSetup()
            ->findOne();

        if (!$connectionEntity) {
            return null;
        }

        return $this->getFactory()
            ->createPunchoutCatalogConnectionMapper()
            ->mapEntityToConnectionTransfer($connectionEntity, new PunchoutCatalogConnectionTransfer());
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch): ?PunchoutCatalogConnectionTransfer
    {
        $connectionCredentialSearch
            ->requireFkCompanyBusinessUnit()
            ->requireFormat()
            ->requireType()
            ->requireUsername();

        $connectionEntity = $this->getFactory()
            ->createPunchoutCatalogConnectionQuery()
            ->joinWithPgwPunchoutCatalogConnectionCart()
            ->joinWithPgwPunchoutCatalogConnectionSetup()
            ->filterByFkCompanyBusinessUnit($connectionCredentialSearch->getFkCompanyBusinessUnit())
            ->filterByFormat($connectionCredentialSearch->getFormat())
            ->filterByType($connectionCredentialSearch->getType())
            ->filterByUsername($connectionCredentialSearch->getUsername())
            ->findOne();

        if (!$connectionEntity) {
            return null;
        }

        return $this->getFactory()
            ->createPunchoutCatalogConnectionMapper()
            ->mapEntityToConnectionTransfer($connectionEntity, new PunchoutCatalogConnectionTransfer());
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer
     */
    public function findConnections(PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer): PunchoutCatalogConnectionListTransfer
    {
        $query = $this->getFactory()->createPunchoutCatalogConnectionQuery()
            ->joinWithPgwPunchoutCatalogConnectionCart()
            ->joinWithPgwPunchoutCatalogConnectionSetup();

        if ($punchoutCatalogConnectionCriteriaTransfer->getFkCompany()) {
            $query->filterByFkCompany($punchoutCatalogConnectionCriteriaTransfer->getFkCompany());
        }

        $connections = $query->find();

        if (!$connections) {
            return new PunchoutCatalogConnectionListTransfer();
        }

        $mapper = $this->getFactory()->createPunchoutCatalogConnectionMapper();

        $connectionList = new PunchoutCatalogConnectionListTransfer();
        foreach ($connections as $connection) {
            $connectionList->addConnection($mapper->mapEntityToConnectionTransfer($connection, new PunchoutCatalogConnectionTransfer()));
        }

        return $connectionList;
    }
    
    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer
     */
    public function findCompanyUserByCustomer(CompanyUserTransfer $companyUserTransfer): bool
    {
        $companyUserTransfer
            ->requireFkCompanyBusinessUnit()
            ->requireFkCustomer();
        
        $companyUserQuery = $this->getFactory()
            ->createCompanyBusinessUnitQuery()
            ->useCompanyUserQuery();
        
        return $companyUserQuery
            ->filterByFkCustomer($companyUserTransfer->getFkCustomer())
            ->endUse()
            ->filterByIdCompanyBusinessUnit($companyUserTransfer->getFkCompanyBusinessUnit())
            ->exists();
    }
}
