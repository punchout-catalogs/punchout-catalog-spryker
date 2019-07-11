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

interface PunchoutCatalogRepositoryInterface
{
    /**
     * Specification:
     * - Finds a connection by punchout id.
     * - Returns null if connection does not exist.
     *
     * @param int $connectionId
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionById(int $connectionId): ?PunchoutCatalogConnectionTransfer;

    /**
     * Specification:
     * - Finds a connection by credentials search transfer.
     * - Returns null if connection does not exist.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch): ?PunchoutCatalogConnectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer
     */
    public function findConnections(PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer): PunchoutCatalogConnectionListTransfer;

    /**
     * @module Customer
     *
     * @param string $email
     *
     * @return int|null
     */
    public function findCustomerIdByEmail(string $email): ?int;

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
    public function findIdCompanyUsersInCompany(int $idCustomer, int $idCompany): array;
}
