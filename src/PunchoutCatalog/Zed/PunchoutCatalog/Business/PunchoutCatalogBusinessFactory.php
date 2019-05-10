<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step\PunchoutCatalogCartWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step\PunchoutCatalogConnectionWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step\PunchoutCatalogSetupWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction\Mapper;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Transaction\MapperInterface;
use Spryker\Zed\DataImport\Business\DataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandler;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticator;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessor;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessor;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogDependencyProvider;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 */
class PunchoutCatalogBusinessFactory extends DataImportBusinessFactory
{
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessorInterface
     */
    public function createRequestProcessor(): RequestProcessorInterface
    {
        return new RequestProcessor(
            $this->createConnectionAuthenticator()
        );
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessorInterface
     */
    public function createCartProcessor(): CartProcessorInterface
    {
        return new CartProcessor(
            $this->getRepository()
        );
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface
     */
    public function createConnectionAuthenticator(): ConnectionAuthenticatorInterface
    {
        return new ConnectionAuthenticator(
            $this->getCompanyFacade(),
            $this->getVaultFacade(),
            $this->getRepository()
        );
    }

    /**
     * @return MapperInterface|Mapper
     */
    public function createTransactionMapper(): MapperInterface
    {
        return new Mapper();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface
     */
    public function createUrlHandler(): UrlHandlerInterface
    {
        return new UrlHandler($this->getConfig());
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface
     */
    public function getCompanyFacade(): PunchoutCatalogToCompanyFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    public function getVaultFacade(): PunchoutCatalogToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_VAULT);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    public function getOAuthCustomerFacade(): PunchoutCatalogToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_OAUTH_CUSTOMER);
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImporterInterface
     */
    public function getPunchoutCatalogConnectionDataImport(): DataImporterInterface
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getPunchoutCatalogConnectionDataImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createPunchoutCatalogConnectionWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImporterInterface
     */
    public function getPunchoutCatalogSetupDataImport(): DataImporterInterface
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getPunchoutCatalogSetupDataImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createPunchoutCatalogSetupWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return \Spryker\Zed\DataImport\Business\Model\DataImporterInterface
     */
    public function getPunchoutCatalogCartDataImport(): DataImporterInterface
    {
        $dataImporter = $this->getCsvDataImporterFromConfig(
            $this->getConfig()->getPunchoutCatalogCartDataImporterConfiguration()
        );

        $dataSetStepBroker = $this->createTransactionAwareDataSetStepBroker();
        $dataSetStepBroker
            ->addStep($this->createPunchoutCatalogCartWriterStep());

        $dataImporter->addDataSetStepBroker($dataSetStepBroker);

        return $dataImporter;
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogConnectionWriterStep
     */
    public function createPunchoutCatalogConnectionWriterStep(): PunchoutCatalogConnectionWriterStep
    {
        return new PunchoutCatalogConnectionWriterStep($this->getVaultFacade());
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep
     */
    public function createPunchoutCatalogSetupWriterStep()
    {
        return new PunchoutCatalogSetupWriterStep();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep
     */
    public function createPunchoutCatalogCartWriterStep()
    {
        return new PunchoutCatalogCartWriterStep();
    }
}
