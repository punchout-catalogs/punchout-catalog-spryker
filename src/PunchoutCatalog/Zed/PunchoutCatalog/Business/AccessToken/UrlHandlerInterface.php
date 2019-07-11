<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken;

interface UrlHandlerInterface
{
    /**
     * @param string $accessToken
     * @param string $storeName
     * @param string $locale
     * @param string $returnUrl
     * @return string
     */
    public function getLoginUrl(string $accessToken, string $storeName, string $locale = null, string $returnUrl = null): string;
}
