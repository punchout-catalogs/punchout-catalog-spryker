<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence;

use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnectionQuery;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionQuery;
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
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogTransactionQuery
     */
    public function createPunchoutCatalogTransactionQuery(): EcoPunchoutCatalogTransactionQuery
    {
        return EcoPunchoutCatalogTransactionQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnectionQuery
     */
    public function createPunchoutCatalogConnectionQuery(): EcoPunchoutCatalogConnectionQuery
    {
        return EcoPunchoutCatalogConnectionQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCartQuery
     */
    public function createPunchoutCatalogCartQuery(): EcoPunchoutCatalogCartQuery
    {
        return EcoPunchoutCatalogCartQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetupQuery
     */
    public function createPunchoutCatalogSetupQuery(): EcoPunchoutCatalogSetupQuery
    {
        return EcoPunchoutCatalogSetupQuery::create();
    }
}
