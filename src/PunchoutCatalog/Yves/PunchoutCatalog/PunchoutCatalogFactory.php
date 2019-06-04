<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog;

use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToQuoteClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Service\PunchoutCatalogToUtilUuidGeneratorServiceInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapper;
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperInterface;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\Kernel\Application;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class PunchoutCatalogFactory extends AbstractFactory
{
    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientInterface
     */
    public function getPunchoutCatalogClient(): PunchoutCatalogToPunchoutCatalogClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_PUNCHOUT_CATALOG);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperInterface
     */
    public function getTransferCartMapper(): CartTransferMapperInterface
    {
        return new CartTransferMapper(
            $this->getCartTransferMapperPlugins()
        );
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartTransferMapperPluginInterface[]
     */
    public function getCartTransferMapperPlugins(): array
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::PLUGIN_CART_TRANSFER_MAPPER);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface
     */
    public function getGlossaryStorageClient(): PunchoutCatalogToGlossaryStorageClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_GLOSSARY_STORAGE);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface
     */
    public function getMoneyClient(): PunchoutCatalogToMoneyClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_MONEY);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientInterface
     */
    public function getProductStorageClient(): PunchoutCatalogToProductStorageClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_PRODUCT_STORAGE);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface
     */
    public function getCustomerClient(): PunchoutCatalogToCustomerClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    public function getStore(): Store
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::STORE);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToQuoteClientInterface
     */
    public function getQuoteClient(): PunchoutCatalogToQuoteClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Service\PunchoutCatalogToUtilUuidGeneratorServiceInterface
     */
    public function getUtilUuidGeneratorService(): PunchoutCatalogToUtilUuidGeneratorServiceInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::SERVICE_UTIL_UUID_GENERATOR);
    }

    /**
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    public function getTokenStorage()
    {
        $application = $this->getApplication();

        return $application['security.token_storage'];
    }

    /**
     * @return \Spryker\Yves\Kernel\Application
     */
    public function getApplication(): Application
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::APPLICATION);
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig
     */
    public function getModuleConfig(): PunchoutCatalogConfig
    {
        return $this->getConfig();
    }
}
