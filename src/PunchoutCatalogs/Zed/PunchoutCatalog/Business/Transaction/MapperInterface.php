<?php

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

interface MapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $requestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapRequestTransferToEntityTransfer(
        PunchoutCatalogRequestTransfer $requestTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer $responseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapResponseTransferToEntityTransfer(
        PunchoutCatalogResponseTransfer $responseTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartRequestTransferToEntityTransfer(
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartResponseTransferToEntityTransfer(
        PunchoutCatalogCartResponseTransfer $cartResponseTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer;
}