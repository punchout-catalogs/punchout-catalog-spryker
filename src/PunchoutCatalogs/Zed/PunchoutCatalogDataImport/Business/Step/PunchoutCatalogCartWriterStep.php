<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step;

use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\DataSet\PunchoutCatalogCartDataSet;

class PunchoutCatalogCartWriterStep implements DataImportStepInterface
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
            ->findOneByName($dataSet[PunchoutCatalogCartDataSet::NAME]);

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart $cartEntity */
        $cartEntity = PgwPunchoutCatalogCartQuery::create()
            ->filterByIdPunchoutCatalogConnection($connectionEntity->getIdPunchoutCatalogConnection())
            ->findOneOrCreate();

        $cartEntity->setMappingCart($dataSet[PunchoutCatalogCartDataSet::MAPPING] ?? null);
        $cartEntity->setMaxDescriptionLength($dataSet[PunchoutCatalogCartDataSet::MAX_DESCRIPTION_LENGTH] ?? null);
        $cartEntity->setDefaultSupplierId($dataSet[PunchoutCatalogCartDataSet::DEFAULT_SUPPLIER_ID] ?? null);
        $cartEntity->setCartEncoding($dataSet[PunchoutCatalogCartDataSet::ENCODING] ?? null);

        $cartEntity->save();
    }
}
