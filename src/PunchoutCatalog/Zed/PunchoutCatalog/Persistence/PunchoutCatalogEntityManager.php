<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransaction;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogPersistenceFactory getFactory()
 */
class PunchoutCatalogEntityManager extends AbstractEntityManager implements PunchoutCatalogEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogTransactionEntityTransfer): PgwPunchoutCatalogTransactionEntityTransfer
    {
        $transactionEntity = $this->getFactory()
            ->createPunchoutCatalogTransactionQuery()
            ->filterByIdPunchoutCatalogTransaction($punchoutCatalogTransactionEntityTransfer->getIdPunchoutCatalogTransaction())
            ->findOneOrCreate();

        $transactionEntity = $this->getFactory()
            ->createPunchoutCatalogTransactionMapper()
            ->mapTransactionTransferToEntity($punchoutCatalogTransactionEntityTransfer, $transactionEntity);

        $transactionEntity->save();

        $punchoutCatalogTransactionEntityTransfer->setIdPunchoutCatalogTransaction($transactionEntity->getIdPunchoutCatalogTransaction());

        return $punchoutCatalogTransactionEntityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $connectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function saveConnection(PunchoutCatalogConnectionTransfer $connectionTransfer): PunchoutCatalogConnectionTransfer
    {
        if ($connectionTransfer->getUuid()) {
            $spyConnection = $this->getFactory()
                ->createPunchoutCatalogConnectionQuery()
                ->filterByUuid($connectionTransfer->getUuid())
                ->findOneOrCreate();
        } else {
            $spyConnection = new PgwPunchoutCatalogConnection();
        }

        $spyConnection = $this->getFactory()
            ->createPunchoutCatalogConnectionMapper()
            ->mapConnectionTransferToEntity($connectionTransfer, $spyConnection);

        $spyConnection->save();

        $connectionTransfer->setUuid($spyConnection->getUuid());

        return $connectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return void
     */
    public function deleteConnection(PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer): void
    {
        $punchoutCatalogConnectionTransfer->requireUuid();

        $this->deleteConnectionByUuid($punchoutCatalogConnectionTransfer->getUuid());
    }

    /**
     * @param string $uuidConnection
     *
     * @return void
     */
    public function deleteConnectionByUuid(string $uuidConnection): void
    {
        $this->getFactory()
            ->createPunchoutCatalogConnectionQuery()
            ->filterByUuid($uuidConnection)
            ->delete();
    }

    /**
     * @return Propel\Mapper\PunchoutCatalogTransactionMapperInterface|PunchoutCatalogTransactionMapperInterface
     */
    protected function getTransactionMapper()
    {
        return $this->getFactory()->createPunchoutCatalogTransactionMapper();
    }
}