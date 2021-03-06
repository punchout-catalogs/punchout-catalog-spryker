<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

class Mapper implements MapperInterface
{
    protected const TRANSACTION_TYPE_SETUP_REQUEST  = 'setup_request';

    protected const TRANSACTION_TYPE_SETUP_RESPONSE = 'setup_response';

    protected const TRANSACTION_TYPE_TRANSFER_TO_REQUISITION = 'transfer_to_requisition';

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer $responseTransfer
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer|null $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapResponseTransferToEntityTransfer(
        PunchoutCatalogSetupResponseTransfer $responseTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new PgwPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(self::TRANSACTION_TYPE_SETUP_RESPONSE);
        }
    
        $entityTransfer->setStatus($responseTransfer->getIsSuccess());
        
        $content = $responseTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setMessage($content);
    
        $exceptions = $responseTransfer->getExceptions();
        if ($exceptions) {
            $entityTransfer->setErrorMessage(implode("\n", $exceptions));
        }
        
        $context = $responseTransfer->getContext();
        if ($context) {
            $entityTransfer->setConnectionSessionId($context->getPunchoutSessionId());
        }
    
        if ($context && $context->getPunchoutCatalogConnection()) {
            $entityTransfer->setFkPunchoutCatalogConnection(
                $context->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection()
            );
        
            $entityTransfer->setFkCompanyBusinessUnit(
                $context->getPunchoutCatalogConnection()->getFkCompanyBusinessUnit()
            );
        }
        
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer|nul $entityTransfer
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function mapRequestTransferToEntityTransfer(
        PunchoutCatalogSetupRequestTransfer $requestTransfer,
        PgwPunchoutCatalogTransactionEntityTransfer $entityTransfer = null
    ): PgwPunchoutCatalogTransactionEntityTransfer
    {
        if ($entityTransfer === null) {
            $entityTransfer = new PgwPunchoutCatalogTransactionEntityTransfer();
            $entityTransfer->setType(self::TRANSACTION_TYPE_SETUP_REQUEST);
        }
    
        $entityTransfer->setStatus($requestTransfer->getIsSuccess());
        
        $content = $requestTransfer->getContent();
        if (!is_string($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        $entityTransfer->setMessage($content);
        
        //$exceptions = $requestTransfer->getExceptions();
        //if ($exceptions) {
        //    $entityTransfer->setErrorMessage(implode("\n", $exceptions));
        //}
        
        if ($requestTransfer->getCompanyBusinessUnit()) {
            $entityTransfer->setFkCompanyBusinessUnit(
                $requestTransfer->getCompanyBusinessUnit()->getIdCompanyBusinessUnit()
            );
        } elseif ($requestTransfer->getFkCompanyBusinessUnit()) {
            $entityTransfer->setFkCompanyBusinessUnit($requestTransfer->getFkCompanyBusinessUnit());
        }
        
        if ($requestTransfer->getContext()) {
            $context = $requestTransfer->getContext();
            
            $rawData = $context->getRawData();
            if (!is_string($rawData)) {
                $rawData = json_encode($rawData, JSON_PRETTY_PRINT);
            }

            $entityTransfer->setRawData($rawData);
            $entityTransfer->setConnectionSessionId($context->getPunchoutSessionId());
            
            if ($context->getPunchoutCatalogConnection()) {
                $entityTransfer->setFkPunchoutCatalogConnection(
                    $context->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection()
                );
            }
        }
        
        return $entityTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer $cartResponseTransfer
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer|null $entityTransfer
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
            $entityTransfer->setType(self::TRANSACTION_TYPE_TRANSFER_TO_REQUISITION);
        }
    
        $entityTransfer->setStatus($cartResponseTransfer->getIsSuccess());
        
        if ($cartResponseTransfer->getContext()) {
            $context = $cartResponseTransfer->getContext();

            $rawData = $context->getRawData();
            if (isset($rawData['context'])) {
                unset($rawData['context']);
            }
            if (!is_string($rawData)) {
                $rawData = json_encode($rawData, JSON_PRETTY_PRINT);
            }
    
            $entityTransfer->setRawData($rawData);
            $entityTransfer->setConnectionSessionId($context->getPunchoutSessionId());
    
            $content = $context->getContent();
            if (!is_string($content)) {
                $content = json_encode($content, JSON_PRETTY_PRINT);
            }
            $entityTransfer->setMessage($content);
            
            if ($context->getPunchoutCatalogConnection()) {
                $entityTransfer->setFkPunchoutCatalogConnection(
                    $context->getPunchoutCatalogConnection()->getIdPunchoutCatalogConnection()
                );
                $entityTransfer->setFkCompanyBusinessUnit(
                    $context->getPunchoutCatalogConnection()->getFkCompanyBusinessUnit()
                );
            }
        }
    
        $exceptions = $cartResponseTransfer->getExceptions();
        if ($exceptions) {
            $entityTransfer->setErrorMessage(implode("\n", $exceptions));
        }
        
        return $entityTransfer;
    }
}
