<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step;

use Orm\Zed\CompanyBusinessUnit\Persistence\SpyCompanyBusinessUnitQuery;
use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionSetupQuery;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet\PunchoutCatalogConnectionSetupDataSet;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class PunchoutCatalogConnectionSetupWriterStep implements DataImportStepInterface
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
        $connectionEntity = PgwPunchoutCatalogConnectionQuery::create()
            ->findOneByName($dataSet[PunchoutCatalogConnectionSetupDataSet::CONNECTION_NAME]);

        if ($connectionEntity == null) {
            return;
        }
        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $setupEntity */
        $setupEntity = PgwPunchoutCatalogConnectionSetupQuery::create()
            ->filterByIdPunchoutCatalogConnectionSetup(
                (int)$connectionEntity->getIdPunchoutCatalogConnection()
            )
            ->findOneOrCreate();

        $businessUnit = SpyCompanyBusinessUnitQuery::create()->findOneByKey(
            $dataSet[PunchoutCatalogConnectionSetupDataSet::COMPANY_BUSINESS_UNIT_KEY]
        );

        if ($businessUnit && $businessUnit->getIdCompanyBusinessUnit()) {
            $setupEntity->setFkCompanyBusinessUnit($businessUnit->getIdCompanyBusinessUnit());
        } else {
            $setupEntity->setFkCompanyBusinessUnit(null);
        }

        $companyUser = SpyCompanyUserQuery::create()->findOneByKey(
            $dataSet[PunchoutCatalogConnectionSetupDataSet::COMPANY_USER_KEY]
        );
        
        if ($companyUser && $companyUser->getIdCompanyUser()) {
            $setupEntity->setFkCompanyUser($companyUser->getIdCompanyUser());
        } else {
            $setupEntity->setFkCompanyUser(null);
        }

        $setupEntity->setLoginMode($dataSet[PunchoutCatalogConnectionSetupDataSet::SETUP_LOGIN_MODE]);

        $setupEntity->save();
    }
}
