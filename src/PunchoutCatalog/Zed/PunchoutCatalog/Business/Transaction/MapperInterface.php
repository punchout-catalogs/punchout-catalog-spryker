<?php

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

interface MapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $requestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapRequestTransferToEntityTransfer(
        PunchoutCatalogSetupRequestTransfer $requestTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer $responseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapResponseTransferToEntityTransfer(
        PunchoutCatalogSetupResponseTransfer $responseTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartResponseTransferToEntityTransfer(
        PunchoutCatalogCartResponseTransfer $cartResponseTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer;
}