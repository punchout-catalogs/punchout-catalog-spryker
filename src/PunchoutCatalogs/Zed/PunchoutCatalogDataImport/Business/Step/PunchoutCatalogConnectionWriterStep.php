<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step;

use Orm\Zed\Company\Persistence\SpyCompanyQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\DataSet\PunchoutCatalogConnectionDataSet;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface;

class PunchoutCatalogConnectionWriterStep implements DataImportStepInterface
{
    /**
     * @uses \PunchoutCatalogs\Shared\PunchoutCatalog\PunchoutCatalogConfig::VAULT_PASSWORD_DATA_TYPE
     */
    protected const VAULT_PASSWORD_DATA_TYPE = 'punchout-catalog-connection-password';

    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @param \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeInterface $vaultFacade
     */
    public function __construct(PunchoutCatalogDataImportToVaultFacadeInterface $vaultFacade)
    {
        $this->vaultFacade = $vaultFacade;
    }

    /**
     * @module PunchoutCatalog
     * @module Company
     *
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $companyEntity = SpyCompanyQuery::create()
            ->findOneByKey($dataSet[PunchoutCatalogConnectionDataSet::COMPANY_KEY]);

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $connectionEntity */
        $connectionEntity = PgwPunchoutCatalogConnectionQuery::create()
            ->filterByFkCompany($companyEntity->getIdCompany())
            ->filterByType($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_TYPE])
            ->filterByFormat($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_FORMAT])
            ->filterByUsername($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_USERNAME])
            ->filterByName($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_NAME])
            ->findOneOrCreate();

        $connectionEntity
            ->setIsActive($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_IS_ACTIVE])
            ->setMappingRequest($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_MAPPING_REQUEST] ?? null)
            ->setCredentials($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_CREDENTIALS] ?? null);

        $connectionEntity->save();

        $this->vaultFacade->store(static::VAULT_PASSWORD_DATA_TYPE, $connectionEntity->getIdPunchoutCatalogConnection(), $dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_PASSWORD]);
    }
}
