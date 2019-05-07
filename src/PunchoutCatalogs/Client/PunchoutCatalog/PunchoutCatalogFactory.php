<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Client\PunchoutCatalog;

use Spryker\Client\Kernel\AbstractFactory;
use PunchoutCatalogs\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface;
use PunchoutCatalogs\Client\PunchoutCatalog\Zed\PunchoutCatalogStub;
use PunchoutCatalogs\Client\PunchoutCatalog\Zed\PunchoutCatalogStubInterface;

class PunchoutCatalogFactory extends AbstractFactory
{
    /**
     * @return \PunchoutCatalogs\Client\PunchoutCatalog\Zed\PunchoutCatalogStubInterface
     */
    public function createPunchoutCatalogStub(): PunchoutCatalogStubInterface
    {
        return new PunchoutCatalogStub($this->getZedRequestClient());
    }

    /**
     * @return \PunchoutCatalogs\Client\PunchoutCatalog\Dependency\Client\PunchoutCatalogToZedRequestClientInterface
     */
    protected function getZedRequestClient(): PunchoutCatalogToZedRequestClientInterface
    {
        return $this->getProvidedDependency(PunchoutCatalogDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
