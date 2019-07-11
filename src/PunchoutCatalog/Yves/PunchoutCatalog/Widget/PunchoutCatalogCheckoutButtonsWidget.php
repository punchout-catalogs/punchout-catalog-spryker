<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Widget;

use Generated\Shared\Transfer\CustomerTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class PunchoutCatalogCheckoutButtonsWidget extends AbstractWidget
{
    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     */
    public function __construct(CustomerTransfer $customerTransfer)
    {
        $this->addParameter('isVisible', $this->isVisible($customerTransfer));
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'PunchoutCatalogCheckoutButtonsWidget';
    }

    /**
     * @return string
     */
    public static function getTemplate(): string
    {
        return '@PunchoutCatalog/views/checkout-buttons/punchout-catalog-checkout-buttons.twig';
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    protected function isVisible(CustomerTransfer $customerTransfer): bool
    {
        return !empty($customerTransfer->getPunchoutCatalogImpersonationDetails()[PunchoutCatalogConstsInterface::IS_PUNCHOUT]);
    }
}
