<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestContentTypeStrategy;


interface RequestContentTypeStrategyPluginInterface
{
    /**
     * @param $requestContentType
     * @return bool
     */
    public function isApplicable(?string $requestContentType);

    /**
     * @param $requestContentType
     * @return string
     */
    public function getPunchoutCatalogContentType(?string $requestContentType);
}
