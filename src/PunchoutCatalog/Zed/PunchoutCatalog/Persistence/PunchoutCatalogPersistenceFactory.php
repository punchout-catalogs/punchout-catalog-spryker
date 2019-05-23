<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence;

use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCartQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetupQuery;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogTransactionQuery;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogDependencyProvider;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapperInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapper;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapperInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapper;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 */
class PunchoutCatalogPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogConnectionMapperInterface
     */
    public function createPunchoutCatalogConnectionMapper(): PunchoutCatalogConnectionMapperInterface
    {
        return new PunchoutCatalogConnectionMapper();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper\PunchoutCatalogTransactionMapperInterface
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
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCartQuery
     */
    public function createPunchoutCatalogCartQuery(): PgwPunchoutCatalogConnectionCartQuery
    {
        return PgwPunchoutCatalogConnectionCartQuery::create();
    }

    /**
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetupQuery
     */
    public function createPunchoutCatalogSetupQuery(): PgwPunchoutCatalogConnectionSetupQuery
    {
        return PgwPunchoutCatalogConnectionSetupQuery::create();
    }

    /**
     * @return SpyCompanyUserQuery
     */
    public function getCompanyUserQuery(): SpyCompanyUserQuery
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::PROPEL_QUERY_COMPANY_USER);
    }
}
