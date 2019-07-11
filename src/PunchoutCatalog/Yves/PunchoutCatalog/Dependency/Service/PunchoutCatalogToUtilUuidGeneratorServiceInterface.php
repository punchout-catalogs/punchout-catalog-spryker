<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Service;

interface PunchoutCatalogToUtilUuidGeneratorServiceInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function generateUuid5FromObjectId(string $name): string;
}
