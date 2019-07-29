<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step;

use Orm\Zed\CompanyBusinessUnit\Persistence\SpyCompanyBusinessUnitQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionQuery;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet\PunchoutCatalogConnectionDataSet;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class PunchoutCatalogConnectionWriterStep implements DataImportStepInterface
{
    /**
     * @uses \PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConfig::VAULT_TYPE_PUNCHOUT_CATALOG_CONNECTION_PASSWORD
     */
    protected const VAULT_PASSWORD_DATA_TYPE = 'pwg_punchout_catalog_connection.password';

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface $vaultFacade
     */
    public function __construct(PunchoutCatalogToVaultFacadeInterface $vaultFacade)
    {
        $this->vaultFacade = $vaultFacade;
    }

    /**
     * @module CompanyBusinessUnit
     *
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $businessUnit = SpyCompanyBusinessUnitQuery::create()->findOneByKey(
            $dataSet[PunchoutCatalogConnectionDataSet::COMPANY_BUSINESS_UNIT_KEY]
        );

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $connectionEntity */
        $connectionEntity = PgwPunchoutCatalogConnectionQuery::create()
            ->filterByFkCompanyBusinessUnit($businessUnit->getIdCompanyBusinessUnit())
            ->filterByType($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_TYPE])
            ->filterByFormat($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_FORMAT])
            ->filterByUsername($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_USERNAME])
            ->filterByName($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_NAME])
            ->findOneOrCreate();

        $connectionEntity
            ->setIsActive($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_IS_ACTIVE])
            ->setMapping($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_MAPPING] ?? null)
            ->setCredentials($dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_CREDENTIALS] ?? null);

        $connectionEntity->save();

        $this->vaultFacade->store(static::VAULT_PASSWORD_DATA_TYPE, $connectionEntity->getIdPunchoutCatalogConnection(), $dataSet[PunchoutCatalogConnectionDataSet::CONNECTION_PASSWORD]);
    }
}
