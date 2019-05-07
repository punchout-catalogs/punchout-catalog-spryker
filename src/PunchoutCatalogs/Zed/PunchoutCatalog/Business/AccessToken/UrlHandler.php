<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\AccessToken;

use PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig;

class UrlHandler implements UrlHandlerInterface
{
    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig
     */
    protected $punchoutCatalogConfig;

    /**
     * @param \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig $punchoutCatalogConfig
     */
    public function __construct(PunchoutCatalogConfig $punchoutCatalogConfig)
    {
        $this->punchoutCatalogConfig = $punchoutCatalogConfig;
    }

    /**
     * @param string $locale
     * @param string $accessToken
     * @param string $returnUrl
     *
     * @return string
     */
    public function getLoginUrl(string $locale, string $accessToken, string $returnUrl): string
    {
        $params = [
            'token' => $accessToken,
            'returnUrl' => $returnUrl,
        ];

        return $this->punchoutCatalogConfig->getYvesLoginUrl() . "?" . http_build_query($params);
    }
}
