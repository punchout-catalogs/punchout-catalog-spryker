<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog;

use Spryker\Client\Customer\CustomerClient;
use PunchoutCatalog\Client\PunchoutCatalog\PunchoutCatalogClient;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientBridge;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToPunchoutCatalogClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToQuoteClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapper;
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperInterface;

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
        return new PunchoutCatalogToPunchoutCatalogClientBridge(
            new PunchoutCatalogClient()
        );
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface
     */
    public function getCustomerClient(): PunchoutCatalogToCustomerClientInterface
    {
        return new PunchoutCatalogToCustomerClientBridge(
            new CustomerClient()
        );
    }

    /**
     * @return \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperInterface
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
}
