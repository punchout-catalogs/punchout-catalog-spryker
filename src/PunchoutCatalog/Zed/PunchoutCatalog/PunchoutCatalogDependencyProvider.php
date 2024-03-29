<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog;

use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToStoreFacadeBridge;
use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeAdapter;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeBridge;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Service\PunchoutCatalogToUtilUuidGeneratorServiceBridge;
use Spryker\Zed\DataImport\DataImportDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogDependencyProvider extends DataImportDependencyProvider
{
    public const FACADE_GLOSSARY = 'FACADE_GLOSSARY';
    public const FACADE_COMPANY_BUSINESS_UNIT = 'FACADE_COMPANY_BUSINESS_UNIT';
    public const FACADE_COMPANY_USER = 'FACADE_COMPANY_USER';
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';
    public const FACADE_VAULT = 'FACADE_VAULT';
    public const FACADE_OAUTH_COMPANY_USER = 'FACADE_OAUTH_COMPANY_USER';
    public const FACADE_STORE = 'FACADE_STORE';
    public const PROPEL_QUERY_COMPANY_USER = 'PROPEL_QUERY_COMPANY_USER';
    public const PROPEL_QUERY_CUSTOMER = 'PROPEL_QUERY_CUSTOMER';
    public const SERVICE_UTIL_UUID_GENERATOR = 'SERVICE_UTIL_UUID_GENERATOR';

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
        $container = $this->addCompanyUserFacade($container);
        $container = $this->addVaultFacade($container);
        $container = $this->addOAuthCompanyUserFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addUtilUuidGeneratorService($container);

        return $container;
    }


    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addUtilUuidGeneratorService(Container $container): Container
    {
        $container[static::SERVICE_UTIL_UUID_GENERATOR] = function (Container $container) {
            return new PunchoutCatalogToUtilUuidGeneratorServiceBridge(
                $container->getLocator()->utilUuidGenerator()->service()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addPropelCustomerQuery($container);
        $container = $this->addPropelCompanyUserQuery($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addGlossaryFacade($container);
        $container = $this->addCompanyBusinessUnitFacade($container);
        $container = $this->addCompanyUserFacade($container);
        $container = $this->addCustomerFacade($container);
        $container = $this->addVaultFacade($container);
        $container = $this->addOAuthCompanyUserFacade($container);
        $container = $this->addStoreFacade($container);

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
            return new PunchoutCatalogToGlossaryFacadeAdapter($container->getLocator()->glossary()->facade());
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
    protected function addCompanyUserFacade(Container $container): Container
    {
        $container[static::FACADE_COMPANY_USER] = function (Container $container) {
            return new PunchoutCatalogToCompanyUserFacadeBridge($container->getLocator()->companyUser()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container[static::FACADE_CUSTOMER] = function (Container $container) {
            return new PunchoutCatalogToCustomerFacadeBridge($container->getLocator()->customer()->facade());
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
    protected function addStoreFacade(Container $container): Container
    {
        $container[static::FACADE_STORE] = function (Container $container) {
            return new PunchoutCatalogToStoreFacadeBridge($container->getLocator()->store()->facade());
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
    protected function addPropelCustomerQuery(Container $container): Container
    {
        $container[static::PROPEL_QUERY_CUSTOMER] = function (): SpyCustomerQuery {
            return SpyCustomerQuery::create();
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
