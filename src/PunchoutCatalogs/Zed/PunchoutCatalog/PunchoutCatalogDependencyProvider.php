<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeBridge;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOAuthCustomerFacadeBridge;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeBridge;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_COMPANY = 'FACADE_COMPANY';
    public const FACADE_VAULT = 'FACADE_VAULT';
    public const FACADE_OAUTH_CUSTOMER = 'FACADE_OAUTH_CUSTOMER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addCompanyFacade($container);
        $container = $this->addVaultFacade($container);
        $container = $this->addOAuthCustomerFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCompanyFacade(Container $container): Container
    {
        $container[static::FACADE_COMPANY] = function (Container $container) {
            return new PunchoutCatalogToCompanyFacadeBridge($container->getLocator()->company()->facade());
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
            return new PunchoutCatalogToVaultFacadeBridge();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOAuthCustomerFacade(Container $container): Container
    {
        $container[static::FACADE_OAUTH_CUSTOMER] = function (Container $container) {
            return new PunchoutCatalogToOAuthCustomerFacadeBridge();
        };

        return $container;
    }
}
