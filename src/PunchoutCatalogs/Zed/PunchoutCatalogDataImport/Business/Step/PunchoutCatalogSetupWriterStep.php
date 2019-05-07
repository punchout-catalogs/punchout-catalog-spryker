<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step;

use Orm\Zed\CompanyBusinessUnit\Persistence\SpyCompanyBusinessUnitQuery;
use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\EcoPunchoutCatalogConnectionQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\EcoPunchoutCatalogSetupQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\DataSet\PunchoutCatalogSetupDataSet;

class PunchoutCatalogSetupWriterStep implements DataImportStepInterface
{
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
        $connectionEntity = EcoPunchoutCatalogConnectionQuery::create()
            ->findOneByName($dataSet[PunchoutCatalogSetupDataSet::CONNECTION_NAME]);

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetup $setupEntity */
        $setupEntity = EcoPunchoutCatalogSetupQuery::create()
            ->filterByIdPunchoutCatalogConnection($connectionEntity->getIdPunchoutCatalogConnection())
            ->findOneOrCreate();

        $businessUnit = SpyCompanyBusinessUnitQuery::create()->findOneByKey($dataSet[PunchoutCatalogSetupDataSet::BUSINESS_UNIT_KEY]);
        if ($businessUnit && $businessUnit->getIdCompanyBusinessUnit()) {
            $setupEntity->setFkBusinessUnit($businessUnit->getIdCompanyBusinessUnit());
        }

        $companyUser = SpyCompanyUserQuery::create()->findOneByKey($dataSet[PunchoutCatalogSetupDataSet::COMPANY_USER_KEY]);
        if ($companyUser && $companyUser->getIdCompanyUser()) {
            $setupEntity->setFkCompanyUser($companyUser->getIdCompanyUser());
        }

        $setupEntity->setLoginMode($dataSet[PunchoutCatalogSetupDataSet::SETUP_LOGIN_MODE]);

        $setupEntity->save();
    }
}
