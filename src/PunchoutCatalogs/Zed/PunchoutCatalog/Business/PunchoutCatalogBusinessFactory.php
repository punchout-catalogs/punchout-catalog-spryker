<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business;

use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Transaction\Mapper;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Transaction\MapperInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\AccessToken\UrlHandler;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticator;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessor;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessorInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessor;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessorInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogDependencyProvider;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 */
class PunchoutCatalogBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessorInterface
     */
    public function createRequestProcessor(): RequestProcessorInterface
    {
        return new RequestProcessor(
            $this->createConnectionAuthenticator()
        );
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Business\CartProcessor\CartProcessorInterface
     */
    public function createCartProcessor(): CartProcessorInterface
    {
        return new CartProcessor(
            $this->getRepository()
        );
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface
     */
    public function createConnectionAuthenticator(): ConnectionAuthenticatorInterface
    {
        return new ConnectionAuthenticator(
            $this->getCompanyFacade(),
            $this->getVaultFacade(),
            $this->getRepository()
        );
    }

    /**
     * @return MapperInterface|Mapper
     */
    public function createTransactionMapper(): MapperInterface
    {
        return new Mapper();
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface
     */
    public function createUrlHandler(): UrlHandlerInterface
    {
        return new UrlHandler($this->getConfig());
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface
     */
    public function getCompanyFacade(): PunchoutCatalogToCompanyFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY);
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    public function getVaultFacade(): PunchoutCatalogToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_VAULT);
    }

    /**
     * @return \PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    public function getOAuthCustomerFacade(): PunchoutCatalogToVaultFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_OAUTH_CUSTOMER);
    }
}
