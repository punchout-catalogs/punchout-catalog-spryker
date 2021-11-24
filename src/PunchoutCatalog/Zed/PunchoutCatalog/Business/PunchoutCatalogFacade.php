<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Generated\Shared\Transfer\PgwPunchoutCatalogTransactionEntityTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCriteriaTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionListTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogEntryPointFilterTransfer;
use Generated\Shared\Transfer\PunchoutCatalogEntryPointTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
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
            $punchoutCatalogRequestTransfer,
            $requestTransaction
        );
        $this->getEntityManager()->saveTransaction($requestTransaction);

        $responseTransaction = $transactionMapper->mapResponseTransferToEntityTransfer(
            $punchoutCatalogResponseTransfer
        );
        $this->getEntityManager()->saveTransaction($responseTransaction);

        return $punchoutCatalogResponseTransfer;
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
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return bool
     */
    public function isOciContent(array $content): bool
    {
        return $this->getFactory()
            ->createOciContentProcessor()
            ->isOciContent($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer
     */
    public function fetchOciHeader(array $content): PunchoutCatalogProtocolDataTransfer
    {
        return $this->getFactory()
            ->createOciContentProcessor()
            ->fetchHeader($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $content
     *
     * @return string
     */
    public function fetchOciOperation(array $content): string
    {
        return $this->getFactory()
            ->createOciContentProcessor()
            ->fetchOperation($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer $punchoutCatalogProtocolDataTransfer
     *
     * @return void
     */
    public function assertOciProtocolData(PunchoutCatalogProtocolDataTransfer $punchoutCatalogProtocolDataTransfer): void
    {
        $this->getFactory()
            ->createOciProtocolDataValidator()
            ->validate($punchoutCatalogProtocolDataTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return bool
     */
    public function isCXmlContent(string $content): bool
    {
        return $this->getFactory()
            ->createCxmlContentProcessor()
            ->isCXmlContent($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return array
     */
    public function fetchCXmlHeaderAsArray(string $content): array
    {
        return $this->getFactory()
            ->createCxmlContentProcessor()
            ->fetchHeaderAsArray($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param string $content
     *
     * @return string|null
     */
    public function fetchCXmlOperation(string $content): ?string
    {
        return $this->getFactory()
            ->createCxmlContentProcessor()
            ->fetchOperation($content);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param PunchoutCatalogEntryPointFilterTransfer $entryPointFilter
     *
     * @return PunchoutCatalogEntryPointTransfer[]
     */
    public function getRequestEntryPointsByBusinessUnit(PunchoutCatalogEntryPointFilterTransfer $entryPointFilter): array
    {
        return $this->getFactory()
            ->createRequestEntryPointReader()
            ->getRequestEntryPointsByBusinessUnit($entryPointFilter);
    }


    /**
     * @api
     *
     * @param string $storeName
     *
     * @return string
     */
    public function getYvesPayloadId(string $storeName): string
    {
        return $this->getPayloadId($this->getYvesHostname($storeName));
    }

    /**
     * @param string $hostName
     *
     * @return string
     */
    protected function getPayloadId(string $hostName): string
    {
        $dti = $this->getTimestamp();

        $randomNumber = rand(1, 999999999);
        $payloadId = $dti . '.' . $randomNumber . '@' . $hostName;

        return $payloadId;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getTimestamp(): string
    {
        return date('Y-m-d\TH:i:sP');
    }

    /**
     * @param string $storeName
     *
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     *
     * @return string
     */
    protected function getYvesHostname(string $storeName): string
    {
        $yvesUrl = $this->getConfig()->getBaseUrlYvesByStore($storeName);

        return parse_url($yvesUrl)['host'];
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig
     */
    protected function getConfig()
    {
        return $this->getFactory()->getConfig();
    }

    /**
     * @api
     *
     * @return string
     */
    public function getZedPayloadId(): string
    {
        return $this->getPayloadId($this->getZedHostname());
    }

    /**
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     *
     * @return string
     */
    protected function getZedHostname(): string
    {
        $zedUrl = $this->getConfig()->getBaseUrlZed();

        return parse_url($zedUrl)['host'];
    }

    /**
     * @param string $mapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    public function convertToMappingTransfer(string $mapping): PunchoutCatalogMappingTransfer
    {
        return ($this->getFactory()->createMappingConverter())->convert(
            $this->convertToArray($mapping)
        );
    }

    /**
     * @param string $mapping
     *
     * @return array|null
     */
    public function convertToArray(string $mapping): ?array
    {
        $mapping = json_decode(trim($mapping), true);

        if (is_array($mapping) && !empty($mapping['cart_item']) && empty($mapping['multi_lines'])) {
            $mapping['cart_item']['multi_lines'] = true;
        }

        return $mapping;
    }
}
