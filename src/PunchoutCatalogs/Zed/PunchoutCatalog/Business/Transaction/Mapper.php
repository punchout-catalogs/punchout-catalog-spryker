<?php

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutTransactionConstsInterface;

class Mapper implements MapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer $responseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapResponseTransferToEntityTransfer(
        PunchoutCatalogResponseTransfer $responseTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new EcoPunchoutCatalogTransactionEntityTransfer();
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
        if ($responseTransfer->getIsSuccess()) {
            $entityTransfer->setStatus(PunchoutTransactionConstsInterface::STATUS_SUCCESS);
        } else {
            $entityTransfer->setStatus(PunchoutTransactionConstsInterface::STATUS_FAILURE);
        }
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $requestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapRequestTransferToEntityTransfer(
        PunchoutCatalogRequestTransfer $requestTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new EcoPunchoutCatalogTransactionEntityTransfer();
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
        if ($requestTransfer->getIsSuccess()) {
            $entityTransfer->setStatus(PunchoutTransactionConstsInterface::STATUS_SUCCESS);
        } else {
            $entityTransfer->setStatus(PunchoutTransactionConstsInterface::STATUS_FAILURE);
        }
        if ($requestTransfer->getPunchoutCatalogConnection()) {
            $entityTransfer->setFkPunchoutCatalogConnection($requestTransfer->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
        }
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartRequestTransferToEntityTransfer(
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new EcoPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(PunchoutTransactionConstsInterface::TRANSACTION_TYPE_SETUP_REQUEST);
        }
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function mapCartResponseTransferToEntityTransfer(
        PunchoutCatalogCartResponseTransfer $cartResponseTransfer,
        EcoPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): EcoPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new EcoPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(PunchoutTransactionConstsInterface::TRANSACTION_TYPE_SETUP_REQUEST);
        }
        $content = $cartResponseTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setMessage($content);
        return $entityTransfer;
    }

}