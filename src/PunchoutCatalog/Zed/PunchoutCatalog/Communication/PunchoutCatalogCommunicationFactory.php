<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandler;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken\UrlHandlerInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyDynamic;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategySingle;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Decoder as OciDecoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Encoder as OciEncoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder as XmlDecoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder as XmlEncoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestContentTypeStrategy\FormTypeStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestContentTypeStrategy\XmlTypeStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator as XmlProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci\ProtocolDataValidator as OciProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToStoreFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface getRepository()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogEntityManagerInterface getEntityManager()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 */
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
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyUserFacadeInterface
     */
    public function getCompanyUserFacade(): PunchoutCatalogToCompanyUserFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY_USER);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Customer\CustomerModeStrategyInterface
     */
    public function createCustomerLoginDynamicStrategy(): CustomerModeStrategyInterface
    {
        return new CustomerModeStrategyDynamic(
            $this->getRepository(),
            $this->getCompanyUserFacade(),
            $this->getCustomerFacade(),
            $this->getCompanyBusinessUnitFacade()
        );
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCustomerFacadeInterface
     */
    public function getCustomerFacade(): PunchoutCatalogToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface
     */
    public function getCompanyBusinessUnitFacade(): PunchoutCatalogToCompanyBusinessUnitFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_COMPANY_BUSINESS_UNIT);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToStoreFacadeInterface
     */
    public function getStoreFacade(): PunchoutCatalogToStoreFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestContentTypeStrategy\RequestContentTypeStrategyPluginInterface[]
     */
    public function createRequestContentTypeStrategyPlugins(): array
    {
        return [
            new FormTypeStrategyPlugin(),
            new XmlTypeStrategyPlugin(),
        ];
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToOauthCompanyUserFacadeInterface
     */
    public function getOauthCompanyUserFacade(): PunchoutCatalogToOauthCompanyUserFacadeInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::FACADE_OAUTH_COMPANY_USER);
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder
     */
    public function createXmlEncoder()
    {
        return new XmlEncoder();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Encoder
     */
    public function createOciEncoder()
    {
        return new OciEncoder();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder
     */
    public function createXmlDecoder()
    {
        return new XmlDecoder();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Decoder
     */
    public function createOciDecoder()
    {
        return new OciDecoder();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator
     */
    public function createXmlProtocolDataValidator()
    {
        return new XmlProtocolDataValidator();
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Oci\ProtocolDataValidator
     */
    public function createOciProtocolDataValidator()
    {
        return new OciProtocolDataValidator();
    }
}
