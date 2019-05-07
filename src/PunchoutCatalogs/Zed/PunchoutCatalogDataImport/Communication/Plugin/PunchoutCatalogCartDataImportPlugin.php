<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Communication\Plugin;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use Generated\Shared\Transfer\DataImporterReportTransfer;
use Spryker\Zed\DataImport\Dependency\Plugin\DataImportPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportConfig;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\PunchoutCatalogDataImportFacadeInterface getFacade()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportConfig getConfig()
 */
class PunchoutCatalogCartDataImportPlugin extends AbstractPlugin implements DataImportPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\DataImporterConfigurationTransfer|null $dataImporterConfigurationTransfer
     *
     * @return \Generated\Shared\Transfer\DataImporterReportTransfer
     */
    public function import(?DataImporterConfigurationTransfer $dataImporterConfigurationTransfer = null): DataImporterReportTransfer
    {
        return $this->getFacade()->importCart($dataImporterConfigurationTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getImportType(): string
    {
        return PunchoutCatalogDataImportConfig::IMPORT_TYPE_PUNCHOUT_CATALOG_CART;
    }
}
