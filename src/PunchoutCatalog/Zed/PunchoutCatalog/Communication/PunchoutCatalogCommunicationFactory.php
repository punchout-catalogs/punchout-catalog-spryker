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
     * @param string $mode
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface
     */
    public function createCustomerLoginModeStrategy(
        string $mode = PunchoutConnectionConstsInterface::CUSTOMER_LOGIN_MODE_SINGLE
    ): CustomerModeStrategyInterface
    {
        switch ($mode) {
            case PunchoutConnectionConstsInterface::CUSTOMER_LOGIN_MODE_SINGLE:
                return new CustomerModeStrategySingle(
                    $this->getCompanyBusinessUnitFacade(),
                    $this->getCustomerFacade()
                );
            case PunchoutConnectionConstsInterface::CUSTOMER_LOGIN_MODE_DYNAMIC:
                return new CustomerModeStrategyDynamic(
                    $this->getCompanyBusinessUnitFacade()
                );
            default:
                throw new InvalidArgumentException(PunchoutConnectionConstsInterface::ERROR_MISSING_LOGIN_MODE);
        }
    }
    
    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface
     */
    public function getCompanyBusinessUnitFacade(): PunchoutCatalogToCompanyBusinessUnitFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY_BUSINESS_UNIT);
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
