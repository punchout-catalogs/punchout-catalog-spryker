<?php

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutTransactionConstsInterface;

class Mapper implements MapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer $responseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapResponseTransferToEntityTransfer(
        PunchoutCatalogResponseTransfer $responseTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new PgwPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(PunchoutTransactionConstsInterface::TRANSACTION_TYPE_SETUP_RESPONSE);
        }
        $requestTransfer = $responseTransfer->getRequest();
        if ($requestTransfer) {
            if ($requestTransfer->getCompany()) {
                $entityTransfer->setFkCompany($requestTransfer->getCompany()->getIdCompany());
            }
            if ($requestTransfer->getPunchoutCatalogConnection()) {
                $entityTransfer->setFkPunchoutCatalogConnection($requestTransfer->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
            }
        }

        $content = $responseTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setMessage($content);
        $entityTransfer->setStatus($responseTransfer->getIsSuccess());
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $requestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapRequestTransferToEntityTransfer(
        PunchoutCatalogRequestTransfer $requestTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new PgwPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(PunchoutTransactionConstsInterface::TRANSACTION_TYPE_SETUP_REQUEST);
        }
        $content = $requestTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setMessage($content);
        if ($requestTransfer->getCompany()) {
            $entityTransfer->setFkCompany($requestTransfer->getCompany()->getIdCompany());
        }
        $entityTransfer->setRawData($requestTransfer->getDecodedContent());
        $entityTransfer->setStatus($requestTransfer->getIsSuccess());
        if ($requestTransfer->getPunchoutCatalogConnection()) {
            $entityTransfer->setFkPunchoutCatalogConnection($requestTransfer->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
        }
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartResponseTransferToEntityTransfer(
        PunchoutCatalogCartResponseTransfer $cartResponseTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new PgwPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(PunchoutTransactionConstsInterface::TRANSACTION_TYPE_TRANSFER_TO_REQUISITION);
        }
        $content = $cartResponseTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setStatus($cartResponseTransfer->getIsSuccess());
        $entityTransfer->setMessage($content);
        return $entityTransfer;
    }

}