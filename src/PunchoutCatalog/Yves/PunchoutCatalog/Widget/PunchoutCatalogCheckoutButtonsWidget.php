<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Widget;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Kernel\Widget\AbstractWidget;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class PunchoutCatalogCheckoutButtonsWidget extends AbstractWidget
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     */
    public function __construct(QuoteTransfer $quoteTransfer)
    {
        $this->addParameter('isVisible', $this->isVisible($quoteTransfer));
        $this->addParameter('quote', $quoteTransfer);
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isVisible(QuoteTransfer $quoteTransfer): bool
    {
        return isset($quoteTransfer->getCustomer()->getPunchoutCatalogImpersonationDetails()['is_punchout']);
    }
}
