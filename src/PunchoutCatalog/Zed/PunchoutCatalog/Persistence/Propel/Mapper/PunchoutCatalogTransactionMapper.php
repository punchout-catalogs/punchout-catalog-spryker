<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction;

class PunchoutCatalogTransactionMapper implements PunchoutCatalogTransactionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction $punchoutCatalogTransaction
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction
     */
    public function mapTransactionTransferToEntity(
        PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer,
        PgwPunchoutCatalogTransaction $punchoutCatalogTransactionEntity
    ): PgwPunchoutCatalogTransaction {
        $punchoutCatalogTransactionEntity->fromArray(
            $punchoutCatalogTransactionEntityTransfer->modifiedToArray(false)
        );

        return $punchoutCatalogTransactionEntity;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction $punchoutCatalogTransactionEntity
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapEntityToTransactionTransfer(
        PgwPunchoutCatalogTransaction $punchoutCatalogTransactionEntity,
        PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
    ): PgwPunchoutCatalogTransactionEntityTransfer {
        $punchoutCatalogTransactionEntityTransfer = $punchoutCatalogTransactionEntityTransfer->fromArray(
            $punchoutCatalogTransactionEntity->toArray(),
            true
        );

        return $punchoutCatalogTransactionEntityTransfer;
    }
}
