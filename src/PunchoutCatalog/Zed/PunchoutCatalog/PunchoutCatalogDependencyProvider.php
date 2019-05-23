<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog;

use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Spryker\Zed\DataImport\DataImportDependencyProvider;
use Spryker\Zed\Kernel\Container;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeBridge;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogDependencyProvider extends DataImportDependencyProvider
{
    public const FACADE_GLOSSARY = 'FACADE_GLOSSARY';
    public const FACADE_COMPANY_BUSINESS_UNIT = 'COMPANY_BUSINESS_UNIT';
    public const FACADE_VAULT = 'FACADE_VAULT';
    public const FACADE_OAUTH_COMPANY_USER = 'FACADE_OAUTH_COMPANY_USER';

    public const PROPEL_QUERY_COMPANY_USER = 'PROPEL_QUERY_COMPANY_USER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addGlossaryFacade($container);
        $container = $this->addCompanyBusinessUnitFacade($container);
        $container = $this->addVaultFacade($container);
        $container = $this->addOAuthCompanyUserFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addPropelCompanyUserQuery($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addGlossaryFacade(Container $container): Container
    {
        $container[self::FACADE_GLOSSARY] = function (Container $container) {
            return new PunchoutCatalogToGlossaryFacadeBridge($container->getLocator()->glossary()->facade());
        };
        
        return $container;
    }
    
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCompanyBusinessUnitFacade(Container $container): Container
    {
        $container[static::FACADE_COMPANY_BUSINESS_UNIT] = function (Container $container) {
            return new PunchoutCatalogToCompanyBusinessUnitFacadeBridge($container->getLocator()->companyBusinessUnit()->facade());
        };

        return $container;
    }
    
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addVaultFacade(Container $container): Container
    {
        $container[static::FACADE_VAULT] = function (Container $container) {
            return new PunchoutCatalogToVaultFacadeBridge($container->getLocator()->vault()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOAuthCompanyUserFacade(Container $container): Container
    {
        $container[static::FACADE_OAUTH_COMPANY_USER] = function (Container $container) {
            return new PunchoutCatalogToOauthCompanyUserFacadeBridge($container->getLocator()->oauthCompanyUser()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPropelCompanyUserQuery(Container $container): Container
    {
        $container[static::PROPEL_QUERY_COMPANY_USER] = function (): SpyCompanyUserQuery {
            return SpyCompanyUserQuery::create();
        };

        return $container;
    }
}
