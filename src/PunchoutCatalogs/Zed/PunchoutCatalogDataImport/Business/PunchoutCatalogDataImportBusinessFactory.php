<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business;

use Spryker\Zed\DataImport\Business\DataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogCartWriterStep;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogConnectionWriterStep;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportDependencyProvider;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportConfig getConfig()
 */
class PunchoutCatalogDataImportBusinessFactory extends DataImportBusinessFactory
{
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
     * @return \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface
     */
    public function getVaultFacade(): PunchoutCatalogDataImportToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDataImportDependencyProvider::FACADE_VAULT);
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogConnectionWriterStep
     */
    public function createPunchoutCatalogConnectionWriterStep(): PunchoutCatalogConnectionWriterStep
    {
        return new PunchoutCatalogConnectionWriterStep($this->getVaultFacade());
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep
     */
    public function createPunchoutCatalogSetupWriterStep()
    {
        return new PunchoutCatalogSetupWriterStep();
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep
     */
    public function createPunchoutCatalogCartWriterStep()
    {
        return new PunchoutCatalogCartWriterStep();
    }
}
