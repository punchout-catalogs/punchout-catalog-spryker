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
     * @return Propel\Mapper\PunchoutCatalogTransactionMapperInterface|PunchoutCatalogTransactionMapperInterface
     */
    protected function getTransactionMapper()
    {
        return $this->getFactory()->createPunchoutCatalogTransactionMapper();
    }
}
