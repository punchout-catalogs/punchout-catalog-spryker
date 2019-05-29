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
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

interface PunchoutCatalogFacadeInterface
{
    /**
     * Specification:
     * - Saves a transaction entity from transaction transfer object.
     * - Returns transaction transfer object
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequest
     *
     * @return \Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer
     */
    public function saveTransaction(PgwPunchoutCatalogTransactionEntityTransfer $punchoutCatalogRequestTransfer): PgwPunchoutCatalogTransactionEntityTransfer;

    /**
     * Specification:
     * - Finds a connection by uuid.
     * - Returns null if connection does not exist.
     *
     * @api
     *
     * @param int $connectionId
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionById(int $connectionId): ?PunchoutCatalogConnectionTransfer;

    /**
     * Specification:
     * - Finds a connection by credentials search transfer.
     * - Returns null if connection does not exist.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch): ?PunchoutCatalogConnectionTransfer;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer;
    
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer;
    
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer
     */
    public function findConnections(PunchoutCatalogConnectionCriteriaTransfer $punchoutCatalogConnectionCriteriaTransfer): PunchoutCatalogConnectionListTransfer;
    
    /**
     * Specification:
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importConnection(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importSetup(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function importCart(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer;


    /**
     * Specification:
     * - Decides if the provided content is OCI.
     *
     * @api
     *
     * @param array $content
     *
     * @return bool
     */
    public function isOciContent(array $content): bool;

    /**
     * Specification:
     * - Fetches header as protocol data from an OCI content.
     *
     * @api
     *
     * @param array $content
     *
     * @return PunchoutCatalogProtocolDataTransfer
     */
    public function fetchOciHeader(array $content): PunchoutCatalogProtocolDataTransfer;

    /**
     * Specification:
     * - Fetches operation name from an OCI content.
     *
     * @api
     *
     * @param array $content
     *
     * @return string
     */
    public function fetchOciOperation(array $content): string;

    /**
     * Specification:
     * - Asserts that all mandatory properties are present in the provided OCI "ProtocolDataTransfer".
     *
     * @api
     *
     * @throws RequiredTransferPropertyException
     *
     * @param PunchoutCatalogProtocolDataTransfer $punchoutCatalogProtocolDataTransfer
     *
     * @return void
     */
    public function assertOciProtocolData(PunchoutCatalogProtocolDataTransfer $punchoutCatalogProtocolDataTransfer): void;
}
