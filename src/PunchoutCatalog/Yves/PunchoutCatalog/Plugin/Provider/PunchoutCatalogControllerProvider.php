<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class PunchoutCatalogControllerProvider extends AbstractYvesControllerProvider
{
    protected const ROUTE_CART_TRANSFER = 'punchout-catalog/cart/transfer';
    protected const ROUTE_CART_CANCEL = 'punchout-catalog/cart/cancel';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app): void
    {
        $this->addCartRoutes();
    }

    /**
     * @uses \PunchoutCatalog\Yves\PunchoutCatalog\Controller\CartController::transferAction()
     * @uses \PunchoutCatalog\Yves\PunchoutCatalog\Controller\CartController::cancelAction()
     *
     * @return $this
     */
    protected function addCartRoutes()
    {
        $this->createController('/{punchoutCatalog}/cart/transfer', static::ROUTE_CART_TRANSFER, 'PunchoutCatalog', 'Cart', 'transfer')
            ->assert('punchoutCatalog', $this->getAllowedLocalesPattern() . 'punchout-catalog|punchout-catalog')
            ->value('punchoutCatalog', 'punchout-catalog');

        $this->createController('/{punchoutCatalog}/cart/cancel', static::ROUTE_CART_CANCEL, 'PunchoutCatalog', 'Cart', 'cancel')
            ->assert('punchoutCatalog', $this->getAllowedLocalesPattern() . 'punchout-catalog|punchout-catalog')
            ->value('punchoutCatalog', 'punchout-catalog');

        return $this;
    }
}
