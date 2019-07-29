<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog;

use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductBundleClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToQuoteClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCartClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Service\PunchoutCatalogToUtilUuidGeneratorServiceBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Plugin\PunchoutCatalog\CartTransferMapperDefaultPlugin;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use Spryker\Yves\Kernel\Plugin\Pimple;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogDependencyProvider extends AbstractBundleDependencyProvider
{
    public const STORE = 'STORE';
    public const CLIENT_GLOSSARY_STORAGE = 'CLIENT_GLOSSARY_STORAGE';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_CART = 'CLIENT_CART';
    public const CLIENT_MONEY = 'CLIENT_MONEY';
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';
    public const CLIENT_PUNCHOUT_CATALOG = 'CLIENT_PUNCHOUT_CATALOG';
    public const CLIENT_PRODUCT_STORAGE = 'CLIENT_PRODUCT_STORAGE';
    public const CLIENT_PRODUCT_BUNDLE = 'CLIENT_PRODUCT_BUNDLE';
    public const SERVICE_UTIL_UUID_GENERATOR = 'SERVICE_UTIL_UUID_GENERATOR';
    public const PLUGIN_CART_TRANSFER_MAPPER = 'PLUGIN_CART_TRANSFER_MAPPER';
    public const APPLICATION = 'APPLICATION';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->provideStore($container);
        $container = $this->addProductBundleClient($container);
        $container = $this->addGlossaryStorageClient($container);
        $container = $this->addMoneyClient($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addCartClient($container);
        $container = $this->addProductStorageClient($container);
        $container = $this->addPunchoutCatalogClient($container);
        $container = $this->addCustomerClient($container);
        $container = $this->addCartTransferMapperPlugins($container);
        $container = $this->addApplication($container);
        $container = $this->addUtilUuidGeneratorService($container);

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
    protected function addCartClient(Container $container): Container
    {
        $container[self::CLIENT_CART] = function (Container $container) {
            return new PunchoutCatalogToCartClientBridge($container->getLocator()->cart()->client());
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
    protected function addPunchoutCatalogClient(Container $container)
    {
        $container[static::CLIENT_PUNCHOUT_CATALOG] = function (Container $container) {
            return new PunchoutCatalogToPunchoutCatalogClientBridge(
                $container->getLocator()->punchOutCatalog()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
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
    protected function addProductBundleClient(Container $container): Container
    {
        // Optional dependency for Product Bundle compatibility
        if (class_exists('\Spryker\Client\ProductBundle\ProductBundleClient')) {
            $container[self::CLIENT_PRODUCT_BUNDLE] = function (Container $container) {
                return new PunchoutCatalogToProductBundleClientBridge($container->getLocator()->productBundle()->client());
            };
        }

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
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCartTransferMapperPlugins(Container $container): Container
    {
        $container[self::PLUGIN_CART_TRANSFER_MAPPER] = function () {
            return $this->getCartTransferMapperPlugins();
        };

        return $container;
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface[]
     */
    protected function getCartTransferMapperPlugins(): array
    {
        return [
            new CartTransferMapperDefaultPlugin($this->getCartItemTransformerPlugins()),
        ];
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface[]
     */
    protected function getCartItemTransformerPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addApplication(Container $container): Container
    {
        $container[static::APPLICATION] = function () {
            return (new Pimple())->getApplication();
        };

        return $container;
    }
}
