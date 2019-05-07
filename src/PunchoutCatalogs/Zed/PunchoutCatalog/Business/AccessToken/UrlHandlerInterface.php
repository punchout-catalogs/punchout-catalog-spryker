<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\AccessToken;

interface UrlHandlerInterface
{
    /**
     * @param string $locale
     * @param string $accessToken
     * @param string $returnUrl
     *
     * @return string
     */
    public function getLoginUrl(string $locale, string $accessToken, string $returnUrl): string;
}
