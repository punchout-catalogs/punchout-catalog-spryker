<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step;

use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet\PunchoutCatalogConnectionCartDataSet;

class PunchoutCatalogConnectionCartWriterStep implements DataImportStepInterface
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
            ->findOneByName($dataSet[PunchoutCatalogConnectionCartDataSet::NAME]);

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart $cartEntity */
        $cartEntity = PgwPunchoutCatalogConnectionCartQuery::create()
            ->filterByIdPunchoutCatalogConnection($connectionEntity->getIdPunchoutCatalogConnection())
            ->findOneOrCreate();

        $cartEntity->setMapping($dataSet[PunchoutCatalogConnectionCartDataSet::MAPPING] ?? null);
        $cartEntity->setMaxDescriptionLength($dataSet[PunchoutCatalogConnectionCartDataSet::MAX_DESCRIPTION_LENGTH] ?? null);
        $cartEntity->setDefaultSupplierId($dataSet[PunchoutCatalogConnectionCartDataSet::DEFAULT_SUPPLIER_ID] ?? null);
        $cartEntity->setEncoding($dataSet[PunchoutCatalogConnectionCartDataSet::ENCODING] ?? null);

        $cartEntity->save();
    }
}
