<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken;

use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig;

class UrlHandler implements UrlHandlerInterface
{
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

        return $this->punchoutCatalogConfig->getYvesHost() . "?" . http_build_query($params);
    }
}
