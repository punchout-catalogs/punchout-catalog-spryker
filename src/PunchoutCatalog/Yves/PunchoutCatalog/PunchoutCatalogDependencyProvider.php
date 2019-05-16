<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog;

use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToQuoteClientBridge;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogDependencyProvider extends AbstractBundleDependencyProvider
{
    public const STORE = 'STORE';
    public const CLIENT_GLOSSARY_STORAGE = 'CLIENT_GLOSSARY_STORAGE';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_MONEY = 'CLIENT_MONEY';
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';
    public const CLIENT_PUNCHOUT_CATALOG = 'CLIENT_PUNCHOUT_CATALOG';
    public const CLIENT_PRODUCT_STORAGE = 'CLIENT_PRODUCT_STORAGE';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->provideStore($container);
        $container = $this->addGlossaryStorageClient($container);
        $container = $this->addMoneyClient($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addProductStorageClient($container);
        $container = $this->addPunchoutCatalogClient($container);
        $container = $this->addCustomerClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function provideStore(Container $container)
    {
        $container[static::STORE] = function () {
            return Store::getInstance();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addProductStorageClient(Container $container): Container
    {
        $container[static::CLIENT_PRODUCT_STORAGE] = function (Container $container) {
            return new PunchoutCatalogToProductStorageClientBridge(
                $container->getLocator()->productStorage()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addGlossaryStorageClient(Container $container): Container
    {
        $container[static::CLIENT_GLOSSARY_STORAGE] = function (Container $container) {
            return new PunchoutCatalogToGlossaryStorageClientBridge(
                $container->getLocator()->glossaryStorage()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMoneyClient($container): Container
    {
        $container[static::CLIENT_MONEY] = function (Container $container) {
            return new PunchoutCatalogToMoneyClientBridge($container->getLocator()->money()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container[static::CLIENT_QUOTE] = function (Container $container) {
            return new PunchoutCatalogToQuoteClientBridge($container->getLocator()->quote()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container)
    {
        $container[static::CLIENT_CUSTOMER] = function (Container $container) {
            return new PunchoutCatalogToCustomerClientBridge(
                $container->getLocator()->customer()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPunchoutCatalogClient(Container $container)
    {
        $container[static::CLIENT_PUNCHOUT_CATALOG] = function (Container $container) {
            return new PunchoutCatalogToPunchoutCatalogClientBridge(
                $container->getLocator()->punchOutCatalog()->client()
            );
        };

        return $container;
    }
}
