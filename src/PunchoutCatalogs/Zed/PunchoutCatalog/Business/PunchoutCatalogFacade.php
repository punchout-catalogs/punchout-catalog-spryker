<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business;

use Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutCatalogBusinessFactory getFactory()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 */
class PunchoutCatalogFacade extends AbstractFacade implements PunchoutCatalogFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequest
     *
     * @return \Generated\Shared\Transfer\EcoPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(EcoPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequest): EcoPunchoutCatalogTransactionEntityTransfer
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
     * @param string $uuidConnection
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByUuid(string $uuidConnection): ?PunchoutCatalogConnectionTransfer
    {
        return $this->getRepository()->findConnectionByUuid($uuidConnection);
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
        if ($punchoutCatalogResponseTransfer->getRequest() !== null) {
            $requestTransaction = $transactionMapper->mapRequestTransferToEntityTransfer($punchoutCatalogResponseTransfer->getRequest(), $requestTransaction);
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
        $transactionMapper = $this->getFactory()->createTransactionMapper();

        $requestTransaction = $transactionMapper->mapCartRequestTransferToEntityTransfer($punchoutCatalogCartRequestTransfer);

        $this->getEntityManager()->saveTransaction($requestTransaction);

        $punchoutCatalogCartResponseTransfer = $this->getFactory()
            ->createCartProcessor()
            ->processCart($punchoutCatalogCartRequestTransfer);

        $responseTransaction = $transactionMapper->mapCartResponseTransferToEntityTransfer($punchoutCatalogCartResponseTransfer);

        $this->getEntityManager()->saveTransaction($responseTransaction);
        if ($punchoutCatalogCartResponseTransfer->getRequest() !== null) {
            $requestTransaction = $transactionMapper->mapCartRequestTransferToEntityTransfer($punchoutCatalogCartResponseTransfer->getRequest(), $requestTransaction);
            $this->getEntityManager()->saveTransaction($requestTransaction);
        }

        return $punchoutCatalogCartResponseTransfer;
    }
}
