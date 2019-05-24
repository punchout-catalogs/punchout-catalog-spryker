<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogDependencyProvider;

use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandler;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategySingle;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyDynamic;

class PunchoutCatalogCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface
     */
    public function createUrlHandler(): UrlHandlerInterface
    {
        return new UrlHandler($this->getConfig());
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface
     */
    public function createCustomerLoginSingleStrategy(): CustomerModeStrategyInterface
    {
        return new CustomerModeStrategySingle(
            $this->getCompanyUserFacade()
        );
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface
     */
    public function createCustomerLoginDynamicStrategy(): CustomerModeStrategyInterface
    {
        return new CustomerModeStrategyDynamic(
            $this->getCompanyUserFacade()
        );
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface
     */
    public function getCompanyUserFacade(): PunchoutCatalogToCompanyUserFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY_USER);
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface
     */
    public function getCustomerFacade(): PunchoutCatalogToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_CUSTOMER);
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeInterface
     */
    public function getOauthCompanyUserFacade(): PunchoutCatalogToOauthCompanyUserFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_OAUTH_COMPANY_USER);
    }
}
