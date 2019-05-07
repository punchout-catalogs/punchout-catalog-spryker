<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use PunchoutCatalogs\Zed\PunchoutCatalog\Exception\MissingYvesLoginUrlConfigurationException;

class PunchoutCatalogConfig extends AbstractBundleConfig
{
    /**
     * @throws \PunchoutCatalogs\Zed\PunchoutCatalog\Exception\MissingYvesLoginUrlConfigurationException
     *
     * @return string
     */
    public function getYvesLoginUrl(): string
    {
        throw new MissingYvesLoginUrlConfigurationException(
            'Missing configuration! You need to configure Yves login URL ' .
            'in your own PunchoutCatalogConfig::getYvesLoginUrl() ' .
            'to be able to generate login URL with access token for remote systems.'
        );
    }
}
