<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\Step;

use Orm\Zed\PunchoutCatalog\Persistence\Base\EcoPunchoutCatalogCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\EcoPunchoutCatalogConnectionQuery;
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
        $connectionEntity = EcoPunchoutCatalogConnectionQuery::create()
            ->findOneByName($dataSet[PunchoutCatalogCartDataSet::CONNECTION_NAME]);

        /** @var \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCart $cartEntity */
        $cartEntity = EcoPunchoutCatalogCartQuery::create()
            ->filterByIdPunchoutCatalogConnection($connectionEntity->getIdPunchoutCatalogConnection())
            ->findOneOrCreate();

        $cartEntity->setMappingCart($dataSet[PunchoutCatalogCartDataSet::CART_MAPPING_CART] ?? null);
        $cartEntity->setMaxDescriptionLength($dataSet[PunchoutCatalogCartDataSet::CART_MAX_DESCRIPTION_LENGTH] ?? null);
        $cartEntity->setCartEncoding($dataSet[PunchoutCatalogCartDataSet::CART_CART_ENCODING] ?? null);

        $cartEntity->save();
    }
}
