<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\AccessToken;

interface UrlHandlerInterface
{
    /**
     * @param string $accessToken
     * @param string $locale
     * @param string $returnUrl
     *
     * @return string
     */
    public function getLoginUrl(string $accessToken, ?string $locale = '', ?string $returnUrl = ''): string;
}
