<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog;

use Spryker\Yves\Kernel\AbstractBundleConfig;

class PunchoutCatalogConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getCustomCartMapping(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCustomCartItemMapping(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCustomCartCustomerMapping(): array
    {
        return [];
    }
}
