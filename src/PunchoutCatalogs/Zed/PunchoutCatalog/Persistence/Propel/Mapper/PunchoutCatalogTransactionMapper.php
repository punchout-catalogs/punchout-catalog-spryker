<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogTransactionTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransaction;

class PunchoutCatalogTransactionMapper implements PunchoutCatalogTransactionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogTransactionTransfer $punchoutCatalogRequestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $ecoPunchoutCatalogTransactionEntityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCatalogRequestTransferToEntityTransfer(
        PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $ecoPunchoutCatalogTransactionEntityTransfer
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        //@TODO implement it
        $ecoPunchoutCatalogTransactionEntityTransfer->setMessage($punchoutCatalogRequestTransfer->getContent());
        if ($punchoutCatalogRequestTransfer->getCompany()) {
            $ecoPunchoutCatalogTransactionEntityTransfer->setFkCompany($punchoutCatalogRequestTransfer->getCompany()->getIdCompany());
        }
        if ($punchoutCatalogRequestTransfer->getIsSuccess()) {
            $ecoPunchoutCatalogTransactionEntityTransfer->setStatus('SUCCESS');
        } else {
            $ecoPunchoutCatalogTransactionEntityTransfer->setStatus('ERROR');
        }
        if ($punchoutCatalogRequestTransfer->getPunchoutCatalogConnection()) {
            $ecoPunchoutCatalogTransactionEntityTransfer->setFkPunchoutCatalogConnection($punchoutCatalogRequestTransfer->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
        }
        return $ecoPunchoutCatalogTransactionEntityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer $punchoutCatalogResponseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $ecoPunchoutCatalogTransactionEntityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCatalogResponseTransferToEntityTransfer(
        PunchoutCatalogResponseTransfer $punchoutCatalogResponseTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $ecoPunchoutCatalogTransactionEntityTransfer
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        //@TODO implement it
        $ecoPunchoutCatalogTransactionEntityTransfer->setMessage($punchoutCatalogResponseTransfer->getContent());
        if ($punchoutCatalogResponseTransfer->getIsSuccess()) {
            $ecoPunchoutCatalogTransactionEntityTransfer->setStatus('SUCCESS');
        } else {
            $ecoPunchoutCatalogTransactionEntityTransfer->setStatus('ERROR');
        }
        if ($punchoutCatalogResponseTransfer->getRequest()
            && $punchoutCatalogResponseTransfer->getRequest()->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection()
            && $punchoutCatalogResponseTransfer->getRequest()->getPunchoutCatalogConnection()) {
            $ecoPunchoutCatalogTransactionEntityTransfer->setFkPunchoutCatalogConnection($punchoutCatalogResponseTransfer->getRequest()->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
        }
        return $ecoPunchoutCatalogTransactionEntityTransfer;
    }


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
    ): PunchoutCatalogTransactionTransfer
    {
        $punchoutCatalogTransactionEntityTransfer = $punchoutCatalogTransactionEntityTransfer->fromArray(
            $punchoutCatalogTransactionEntity->toArray(),
            true
        );

        return $punchoutCatalogTransactionEntityTransfer;
    }


}
