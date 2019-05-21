<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogBusinessFactory getFactory()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 */
class PunchoutCatalogFacade extends AbstractFacade implements PunchoutCatalogFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequest
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequest): PgwPunchoutCatalogTransactionEntityTransfer
    {
        return $this->getEntityManager()
            ->saveTransaction($punchoutCatalogRequest);
    }
    
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $connectionId
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionById(int $connectionId): ?PunchoutCatalogConnectionTransfer
    {
        return $this->getRepository()->findConnectionById($connectionId);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch): ?PunchoutCatalogConnectionTransfer
    {
        return $this->getFactory()
            ->createConnectionAuthenticator()
            ->findConnectionByCredential($connectionCredentialSearch);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer
     */
    public function findConnections(PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer): PunchoutCatalogConnectionListTransfer
    {
        return $this->getRepository()->findConnections($punchoutCatalogConnectionCriteriaTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer
    {
        $context = new PunchoutCatalogCommonContextTransfer();
        $context->setPunchoutSessionId($this->generateSessionId());
        
        $punchoutCatalogRequestTransfer->setContext($context);
        
        $transactionMapper = $this->getFactory()->createTransactionMapper();

        $requestTransaction = $transactionMapper->mapRequestTransferToEntityTransfer(
            $punchoutCatalogRequestTransfer
        );
        $this->getEntityManager()->saveTransaction($requestTransaction);
        
        $punchoutCatalogResponseTransfer = $this->getFactory()
            ->createRequestProcessor()
            ->processRequest($punchoutCatalogRequestTransfer);
    
        $requestTransaction = $transactionMapper->mapRequestTransferToEntityTransfer(
            $punchoutCatalogRequestTransfer, $requestTransaction
        );
        $this->getEntityManager()->saveTransaction($requestTransaction);
        
        $responseTransaction = $transactionMapper->mapResponseTransferToEntityTransfer(
            $punchoutCatalogResponseTransfer
        );
        $this->getEntityManager()->saveTransaction($responseTransaction);

        return $punchoutCatalogResponseTransfer;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        $punchoutCatalogCartResponseTransfer = $this->getFactory()
            ->createCartProcessor()
            ->processCart($punchoutCatalogCartRequestTransfer);
        
        $responseTransaction = $this->getFactory()->createTransactionMapper()
            ->mapCartResponseTransferToEntityTransfer($punchoutCatalogCartResponseTransfer);

        $this->getEntityManager()->saveTransaction($responseTransaction);

        return $punchoutCatalogCartResponseTransfer;
    }
    
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        $punchoutCatalogCartResponseTransfer = $this->getFactory()
            ->createCartProcessor()
            ->processCancel($punchoutCatalogCancelRequestTransfer);
        
        $responseTransaction = $this->getFactory()->createTransactionMapper()
            ->mapCartResponseTransferToEntityTransfer($punchoutCatalogCartResponseTransfer);
        
        $this->getEntityManager()->saveTransaction($responseTransaction);
        
        return $punchoutCatalogCartResponseTransfer;
    }
    
    
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importConnection(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer
    {
        return $this->getFactory()->getPunchoutCatalogConnectionDataImport()->import($dataImporterConfigurationTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importSetup(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer
    {
        return $this->getFactory()->getPunchoutCatalogSetupDataImport()->import($dataImporterConfigurationTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importCart(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer
    {
        return $this->getFactory()->getPunchoutCatalogCartDataImport()->import($dataImporterConfigurationTransfer);
    }
    
    /**
     * @return string
     */
    protected function generateSessionId(): string
    {
        $id = microtime(true) . '_' . uniqid('', true);
        return $this->getFactory()
            ->createUtilUuidGeneratorService()
            ->generateUuid5FromObjectId($id);
    }
}
