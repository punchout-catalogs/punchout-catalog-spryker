<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\Router;

use Spryker\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use Spryker\Yves\Router\Route\RouteCollection;

class PunchoutCatalogRouteProviderPlugin extends AbstractRouteProviderPlugin
{
    protected const ROUTE_CART_TRANSFER = 'punchout-catalog/cart/transfer';
    protected const ROUTE_CART_CANCEL = 'punchout-catalog/cart/cancel';

    /**
     * Specification:
     * - Adds Routes to the RouteCollection.
     *
     * @api
     *
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addCartRoutes($routeCollection);
        return $routeCollection;
    }
    
    
    /**
     * @uses \PunchoutCatalog\Yves\PunchoutCatalog\Controller\CartController::transferAction()
     * @uses \PunchoutCatalog\Yves\PunchoutCatalog\Controller\CartController::cancelAction()
     *
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addCartRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/' . static::ROUTE_CART_TRANSFER, 'PunchoutCatalog', 'Cart', 'transferAction');
        $routeCollection->add(static::ROUTE_CART_TRANSFER, $route);
    
        $route = $this->buildRoute('/' . static::ROUTE_CART_CANCEL, 'PunchoutCatalog', 'Cart', 'cancelAction');
        $routeCollection->add(static::ROUTE_CART_CANCEL, $route);

        return $routeCollection;
    }
}
