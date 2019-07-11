<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\Step;

use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\Base\PgwPunchoutCatalogConnectionQuery;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet\PunchoutCatalogConnectionCartDataSet;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

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

        if ($connectionEntity == null) {
            return;
        }
        /** @var \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $cartEntity */
        $cartEntity = PgwPunchoutCatalogConnectionCartQuery::create()
            ->filterByIdPunchoutCatalogConnectionCart($connectionEntity->getIdPunchoutCatalogConnection())
            ->findOneOrCreate();

        $cartEntity->setMapping($dataSet[PunchoutCatalogConnectionCartDataSet::MAPPING] ?? null);
        $cartEntity->setMaxDescriptionLength($dataSet[PunchoutCatalogConnectionCartDataSet::MAX_DESCRIPTION_LENGTH] ?? null);
        $cartEntity->setDefaultSupplierId($dataSet[PunchoutCatalogConnectionCartDataSet::DEFAULT_SUPPLIER_ID] ?? null);
        $cartEntity->setBundleMode($dataSet[PunchoutCatalogConnectionCartDataSet::BUNDLE_MODE] ?? null);
        $cartEntity->setTotalsMode($dataSet[PunchoutCatalogConnectionCartDataSet::TOTALS_MODE] ?? null);
        $cartEntity->setEncoding($dataSet[PunchoutCatalogConnectionCartDataSet::ENCODING] ?? null);

        $cartEntity->save();
    }
}
