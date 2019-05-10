<?php

/**
 *
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class PunchoutCatalogControllerProvider extends AbstractYvesControllerProvider
{
    protected const ROUTE_PUNCHOUT_CATALOG_CART_TRANSFER = 'punchout-catalog/cart/transfer';
    protected const ROUTE_PUNCHOUT_CATALOG_CART_CANCEL = 'punchout-catalog/cart/cancel';

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
     * @uses \PunchoutCatalog\Yves\PunchoutCatalog\Controller\RequestController
     *
     * @return $this
     */
    protected function addCartRoutes()
    {
        $this->createController('/punchout-catalog/cart/transfer', static::ROUTE_PUNCHOUT_CATALOG_CART_TRANSFER, 'PunchoutCatalog', 'Cart', 'transfer');
        $this->createController('/punchout-catalog/cart/cancel', static::ROUTE_PUNCHOUT_CATALOG_CART_CANCEL, 'PunchoutCatalog', 'Cart', 'cancel');
        return $this;
    }
}
