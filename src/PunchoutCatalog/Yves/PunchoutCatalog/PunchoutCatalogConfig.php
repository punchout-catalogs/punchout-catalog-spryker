<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
        return $this->get(PunchoutCatalogConstants::CUSTOM_CART_TRANSFER_MAPPING, []);
    }

    /**
     * @return array
     */
    public function getCustomCartItemMapping(): array
    {
        return $this->get(PunchoutCatalogConstants::CUSTOM_CART_ITEM_TRANSFER_MAPPING, []);
    }

    /**
     * @return array
     */
    public function getCustomCartCustomerMapping(): array
    {
        return $this->get(PunchoutCatalogConstants::CUSTOM_CART_CUSTOMER_TRANSFER_MAPPING, []);
    }
}
