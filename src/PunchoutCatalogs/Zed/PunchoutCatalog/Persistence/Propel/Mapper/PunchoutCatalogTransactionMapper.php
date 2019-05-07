<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction;

class PunchoutCatalogTransactionMapper implements PunchoutCatalogTransactionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction $punchoutCatalogTransaction
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction
     */
    public function mapTransactionTransferToEntity(
        EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer,
        EcoPunchoutCatalogTransaction $punchoutCatalogTransactionEntity
    ): EcoPunchoutCatalogTransaction
    {
        $punchoutCatalogTransactionEntity->fromArray(
            $punchoutCatalogTransactionEntityTransfer->modifiedToArray(false)
        );
        return $punchoutCatalogTransactionEntity;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction $punchoutCatalogTransactionEntity
     * @param \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapEntityToTransactionTransfer(
        EcoPunchoutCatalogTransaction $punchoutCatalogTransactionEntity,
        EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        $punchoutCatalogTransactionEntityTransfer = $punchoutCatalogTransactionEntityTransfer->fromArray(
            $punchoutCatalogTransactionEntity->toArray(),
            true
        );

        return $punchoutCatalogTransactionEntityTransfer;
    }
}
