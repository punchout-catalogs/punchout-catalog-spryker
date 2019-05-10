<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business;

use Spryker\Zed\DataImport\Business\DataImportBusinessFactory;
use Spryker\Zed\DataImport\Business\Model\DataImporterInterface;
use PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogCartWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogConnectionWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalogDataImport\Business\Step\PunchoutCatalogSetupWriterStep;
use PunchoutCatalog\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportDependencyProvider;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportConfig getConfig()
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
     * @return \PunchoutCatalog\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface
     */
    public function getVaultFacade(): PunchoutCatalogDataImportToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDataImportDependencyProvider::FACADE_VAULT);
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
