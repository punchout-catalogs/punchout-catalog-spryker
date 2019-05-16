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
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapper;
use PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperInterface;
use Spryker\Client\Customer\CustomerClient;
use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Kernel\AbstractFactory;

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
            $this->getGlossaryStorageClient(),
            $this->getMoneyClient(),
            $this->getProductStorageClient(),
            $this->getCustomerClient(),
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
}
