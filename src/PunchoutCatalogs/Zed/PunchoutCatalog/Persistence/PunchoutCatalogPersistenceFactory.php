<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence;

use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapperInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapper;
use PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapperInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapper;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 */
class PunchoutCatalogPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapperInterface
     */
    public function createPunchoutCatalogConnectionMapper(): PunchoutCatalogConnectionMapperInterface
    {
        return new PunchoutCatalogConnectionMapper();
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapperInterface
     */
    public function createPunchoutCatalogTransactionMapper(): PunchoutCatalogTransactionMapperInterface
    {
        return new PunchoutCatalogTransactionMapper();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionQuery
     */
    public function createPunchoutCatalogTransactionQuery(): PgwPunchoutCatalogTransactionQuery
    {
        return PgwPunchoutCatalogTransactionQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionQuery
     */
    public function createPunchoutCatalogConnectionQuery(): PgwPunchoutCatalogConnectionQuery
    {
        return PgwPunchoutCatalogConnectionQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCartQuery
     */
    public function createPunchoutCatalogCartQuery(): PgwPunchoutCatalogCartQuery
    {
        return PgwPunchoutCatalogCartQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogSetupQuery
     */
    public function createPunchoutCatalogSetupQuery(): PgwPunchoutCatalogSetupQuery
    {
        return PgwPunchoutCatalogSetupQuery::create();
    }
}
