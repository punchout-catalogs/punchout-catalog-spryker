<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction;

interface PunchoutCatalogEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function addRequestCatalogTransaction(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer, EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer = null): EcoPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer $punchoutCatalogResponseTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function addResponseCatalogTransaction(PunchoutCatalogResponseTransfer $punchoutCatalogResponseTransfer): EcoPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer): EcoPunchoutCatalogTransactionEntityTransfer;

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
