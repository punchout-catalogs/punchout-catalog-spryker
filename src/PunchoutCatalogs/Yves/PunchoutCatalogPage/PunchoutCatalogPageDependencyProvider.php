<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage;

use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientBridge;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientBridge;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToQuoteClientBridge;

/**
 * @method \PunchoutCatalogs\Yves\PunchoutCatalogPage\PunchoutCatalogPageConfig getConfig()
 */
class PunchoutCatalogPageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const STORE = 'STORE';
    public const CLIENT_GLOSSARY_STORAGE = 'CLIENT_GLOSSARY_STORAGE';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_MONEY = 'CLIENT_MONEY';

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
    protected function addGlossaryStorageClient(Container $container): Container
    {
        $container[static::CLIENT_GLOSSARY_STORAGE] = function (Container $container) {
            return new PunchoutCatalogPageToGlossaryStorageClientBridge(
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
            return new PunchoutCatalogPageToMoneyClientBridge($container->getLocator()->money()->client());
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
            return new PunchoutCatalogPageToQuoteClientBridge($container->getLocator()->quote()->client());
        };

        return $container;
    }
}
