<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Client\PunchoutCatalog;

use Spryker\Client\Kernel\AbstractFactory;
use PunchoutCatalog\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface;
use PunchoutCatalog\Client\PunchoutCatalog\Zed\PunchoutCatalogStub;
use PunchoutCatalog\Client\PunchoutCatalog\Zed\PunchoutCatalogStubInterface;

class PunchoutCatalogFactory extends AbstractFactory
{
    /**
     * @return \PunchoutCatalog\Client\PunchoutCatalog\Zed\PunchoutCatalogStubInterface
     */
    public function createPunchoutCatalogStub(): PunchoutCatalogStubInterface
    {
        return new PunchoutCatalogStub($this->getZedRequestClient());
    }

    /**
     * @return \PunchoutCatalog\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface
     */
    protected function getZedRequestClient(): PunchoutCatalogToZedRequestClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_ZED_REQUEST);
    }
    
    /**
     * @return \PunchoutCatalog\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface
     */
    public function getCustomerClient(): PunchoutCatalogToCustomerClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_CUSTOMER);
    }
}
