<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Propel\Runtime\ActiveQuery\Criteria;

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
            //->requireFkCompanyBusinessUnit()
            ->requireFormat()
            ->requireType()
            ->requireUsername();

        $connectionQuery = $this->getFactory()
            ->createPunchoutCatalogConnectionQuery()
            ->joinWithPgwPunchoutCatalogConnectionCart()
            ->joinWithPgwPunchoutCatalogConnectionSetup()
            ->filterByFormat($connectionCredentialSearch->getFormat())
            ->filterByType($connectionCredentialSearch->getType())
            ->filterByUsername($connectionCredentialSearch->getUsername(), Criteria::IN)
            ->filterByIsActive(1);

        if ($connectionCredentialSearch->getFkCompanyBusinessUnit()) {
            $connectionQuery->filterByFkCompanyBusinessUnit($connectionCredentialSearch->getFkCompanyBusinessUnit());
        }

        $countQuery = clone $connectionQuery;
        if ($countQuery->count() !== 1) {
            return null;
        }

        $connectionEntity = $connectionQuery->findOne();

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
     * @module Customer
     *
     * @param string $email
     *
     * @return int|null
     */
    public function findCustomerIdByEmail(string $email): ?int
    {
        $query = $this->getFactory()
            ->getCustomerQuery()
            //->filterByIsActive(true)
            ->filterByEmail($email);
            //->joinCompany()
            //->useCompanyQuery()
            //->joinCompanyBusinessUnit()
            //->useCompanyBusinessUnitQuery()
            //->filterByIdCompanyBusinessUnit($idCompanyBusinessUnit)
            //->endUse()
            //->endUse()


        $customer = $query->findOne();
        if ($customer === null) {
            return null;
        }

        return $customer->getIdCustomer();
    }

    /**
     * @module CompanyUser
     * @module Company
     * @module Customer
     * @module CompanyBusinessUnit
     *
     * @param int $idCustomer
     * @param int $idCompany
     *
     * @return array
     */
    public function findIdCompanyUsersInCompany(int $idCustomer, int $idCompany): array
    {
        $query = $this->getFactory()
            ->getCompanyUserQuery()
            ->select('id_company_user')
            ->filterByIsActive(true)
            ->filterByFkCustomer($idCustomer)
            ->filterByFkCompany($idCompany);

        return $query->find()->toArray();
    }
}
