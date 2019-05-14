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
use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function saveConnection(PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer): PunchoutCatalogConnectionTransfer
    {
        return $this->getEntityManager()
            ->saveConnection($punchoutCatalogConnectionTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return void
     */
    public function deleteConnection(PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer): void
    {
        $this->getEntityManager()
            ->deleteConnection($punchoutCatalogConnectionTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $uuidConnection
     *
     * @return void
     */
    public function deleteConnectionByUuid(string $uuidConnection): void
    {
        $this->getEntityManager()
            ->deleteConnectionByUuid($uuidConnection);
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function processRequest(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogResponseTransfer
    {
        $transactionMapper = $this->getFactory()->createTransactionMapper();

        $requestTransaction = $transactionMapper->mapRequestTransferToEntityTransfer($punchoutCatalogRequestTransfer);

        $this->getEntityManager()->saveTransaction($requestTransaction);

        $punchoutCatalogResponseTransfer = $this->getFactory()
            ->createRequestProcessor()
            ->processRequest($punchoutCatalogRequestTransfer);

        $responseTransaction = $transactionMapper->mapResponseTransferToEntityTransfer($punchoutCatalogResponseTransfer);

        $this->getEntityManager()->saveTransaction($responseTransaction);
        if ($punchoutCatalogResponseTransfer->getContext() !== null
            && $punchoutCatalogResponseTransfer->getContext()->getRequest() !== null) {
            $requestTransaction = $transactionMapper->mapRequestTransferToEntityTransfer($punchoutCatalogResponseTransfer->getContext()->getRequest(), $requestTransaction);
            $this->getEntityManager()->saveTransaction($requestTransaction);
        }

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
}
