<?php

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
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
        $requestTransfer = $responseTransfer->getContext()->getRequest();
        if ($requestTransfer) {
            if ($requestTransfer->getCompanyBusinessUnit()) {
                $entityTransfer->setFkCompanyBusinessUnit(
                    $requestTransfer->getCompanyBusinessUnit()->getIdCompanyBusinessUnit()
                );
            }
            if ($requestTransfer->getPunchoutCatalogConnection()) {
                $entityTransfer->setFkPunchoutCatalogConnection(
                    $requestTransfer->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection()
                );
            }
        }
        $entityTransfer->setConnectionSessionId($responseTransfer->getContext()->getConnectionSessionId());

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

        if ($requestTransfer->getCompanyBusinessUnit()) {
            $entityTransfer->setFkCompanyBusinessUnit(
                $requestTransfer->getCompanyBusinessUnit()->getIdCompanyBusinessUnit()
            );
        }

        if ($requestTransfer->getContext()) {
            $rawData = $requestTransfer->getContext()->getRawData();

            if (!is_string($rawData)) {
                $rawData = json_encode($rawData, JSON_PRETTY_PRINT);
            }

            $entityTransfer->setRawData($rawData);
            $entityTransfer->setConnectionSessionId($requestTransfer->getContext()->getConnectionSessionId());
        }
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
    
        $entityTransfer->setStatus($cartResponseTransfer->getIsSuccess());
        
        if ($cartResponseTransfer->getContext()) {
            $content = $cartResponseTransfer->getContext()->getRawData();
            if (!is_string($content)) {
                $content = json_encode($content, JSON_PRETTY_PRINT);
            }
            $entityTransfer->setMessage($content);
            
            $rawData = $cartResponseTransfer->getContext()->getRequest();
            $rawData = $rawData ? $rawData->toArray() : [];
    
            if (!is_string($rawData)) {
                $rawData = json_encode($rawData, JSON_PRETTY_PRINT);
            }
            $entityTransfer->setRawData($rawData);
            
            //@todo: @Dima improve method how to bypass connection - better use an own property in context, don't set request
            if ($cartResponseTransfer->getContext()->getRequest() && $cartResponseTransfer->getContext()->getRequest()->getContext()->getPunchoutCatalogConnection()) {
                $entityTransfer->setFkPunchoutCatalogConnection($cartResponseTransfer->getContext()->getRequest()->getContext()->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection());
                $entityTransfer->setFkCompanyBusinessUnit($cartResponseTransfer->getContext()->getRequest()->getContext()->getPunchoutCatalogConnection()->getFkCompanyBusinessUnit());
            }
    
            $entityTransfer->setConnectionSessionId($cartResponseTransfer->getContext()->getConnectionSessionId());
        }
        
        return $entityTransfer;
    }
}
