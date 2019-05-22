<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken;

use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig;

class UrlHandler implements UrlHandlerInterface
{
    protected const USER_LOGIN_URL = 'access-token';
    
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig
     */
    protected $punchoutCatalogConfig;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig $punchoutCatalogConfig
     */
    public function __construct(PunchoutCatalogConfig $punchoutCatalogConfig)
    {
        $this->punchoutCatalogConfig = $punchoutCatalogConfig;
    }

    /**
     * @param string $accessToken
     * @param string $locale
     *
     * @return string
     */
    public function getLoginUrl(string $accessToken, string $locale = null): string
    {
        $locale = $locale ?? $this->punchoutCatalogConfig->getDefaultStoreLocale();
        
        return $this->punchoutCatalogConfig->getBaseUrlYves()
            . '/' . $locale
            . '/' . static::USER_LOGIN_URL
            . '/' . $accessToken;
    }
}
