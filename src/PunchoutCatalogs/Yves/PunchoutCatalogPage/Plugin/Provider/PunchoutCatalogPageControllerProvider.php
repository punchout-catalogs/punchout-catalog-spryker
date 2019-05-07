<?php

/**
 *
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class PunchoutCatalogPageControllerProvider extends AbstractYvesControllerProvider
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
     * @uses \PunchoutCatalogs\Yves\PunchoutCatalogPage\Controller\RequestController
     *
     * @return $this
     */
    protected function addCartRoutes()
    {
        $this->createController('/punchout-catalog/cart/transfer', static::ROUTE_PUNCHOUT_CATALOG_CART_TRANSFER, 'PunchoutCatalogPage', 'Cart', 'transfer');
        $this->createController('/punchout-catalog/cart/cancel', static::ROUTE_PUNCHOUT_CATALOG_CART_CANCEL, 'PunchoutCatalogPage', 'Cart', 'cancel');
        return $this;
    }
}
