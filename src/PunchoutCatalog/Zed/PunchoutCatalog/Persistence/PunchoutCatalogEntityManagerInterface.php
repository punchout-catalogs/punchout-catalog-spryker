<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction;

interface PunchoutCatalogEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer): PgwPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $connectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function saveConnection(PunchoutCatalogConnectionTransfer $connectionTransfer): PunchoutCatalogConnectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $connectionTransfer
     *
     * @return void
     */
    public function deleteConnection(PunchoutCatalogConnectionTransfer $connectionTransfer): void;

    /**
     * @param string $uuidConnection
     *
     * @return void
     */
    public function deleteConnectionByUuid(string $uuidConnection): void;
}
