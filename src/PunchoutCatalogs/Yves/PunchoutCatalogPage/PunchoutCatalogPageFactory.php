<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage;

use Spryker\Client\Customer\CustomerClient;
use PunchoutCatalogs\Client\PunchoutCatalog\PunchoutCatalogClient;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToCustomerClientBridge;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToCustomerClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToPunchoutCatalogClientBridge;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToPunchoutCatalogClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToQuoteClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Mapper\CartTransferMapper;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Mapper\CartTransferMapperInterface;

/**
 * @method \PunchoutCatalogs\Yves\PunchoutCatalogPage\PunchoutCatalogPageConfig getConfig()
 */
class PunchoutCatalogPageFactory extends AbstractFactory
{
    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToPunchoutCatalogClientInterface
     */
    public function getPunchoutCatalogClient(): PunchoutCatalogPageToPunchoutCatalogClientInterface
    {
        return new PunchoutCatalogPageToPunchoutCatalogClientBridge(
            new PunchoutCatalogClient()
        );
    }

    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToCustomerClientInterface
     */
    public function getCustomerClient(): PunchoutCatalogPageToCustomerClientInterface
    {
        return new PunchoutCatalogPageToCustomerClientBridge(
            new CustomerClient()
        );
    }

    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Mapper\CartTransferMapperInterface
     */
    public function getTransferCartMapper(): CartTransferMapperInterface
    {
        return new CartTransferMapper(
            $this->getGlossaryStorageClient(),
            $this->getMoneyClient(),
            $this->getStore()->getCurrentLocale()
        );
    }

    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientInterface
     */
    public function getGlossaryStorageClient(): PunchoutCatalogPageToGlossaryStorageClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogPageDependencyProvider::CLIENT_GLOSSARY_STORAGE);
    }

    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientInterface
     */
    public function getMoneyClient(): PunchoutCatalogPageToMoneyClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogPageDependencyProvider::CLIENT_MONEY);
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    public function getStore(): Store
    {
        return $this->getProvidedDependency(PunchoutCatalogPageDependencyProvider::STORE);
    }

    /**
     * @return \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToQuoteClientInterface
     */
    public function getQuoteClient(): PunchoutCatalogPageToQuoteClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogPageDependencyProvider::CLIENT_QUOTE);
    }
}
