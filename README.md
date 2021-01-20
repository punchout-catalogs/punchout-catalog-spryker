# PunchOutCatalog Module
[![Build Status](https://travis-ci.org/punchout-catalogs/punchout-catalog-spryker.svg)](https://travis-ci.org/punchout-catalogs/punchout-catalog-spryker)
[![Coverage Status](https://coveralls.io/repos/github/punchout-catalogs/punchout-catalog-spryker/badge.svg)](https://coveralls.io/github/punchout-catalogs/punchout-catalog-spryker)

Punchout Catalog Module for Spryker eCommerce Platform

## Installation

```
composer require punchout-catalogs/punchout-catalog-spryker
```

## Documentation

[Integration Documentation](https://documentation.spryker.com/docs/punchout-catalog-feature-integration)
[Spryker Documentation](https://academy.spryker.com/developing_with_spryker/module_guide/modules.html)

## Testing

Running:

```
./vendor/bin/codecept run
```


### Custom cart mapping

Extending cart mapping behavior could be implemented by overriding 
PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig class method:

```php
<?php

namespace Pyz\Yves\PunchoutCatalog;

use PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogConfig as BasePunchoutCatalogConfig;

class PunchoutCatalogConfig extends BasePunchoutCatalogConfig
{
    /**
     * @return array
     */
    public function getCustomCartMapping(): array
    {
        return [
            // QuoteTransfer => PunchoutCatalogDocumentCartTransfer

            // without key, should return transfer object
            function ($quoteTransfer, $cartRequestTransfer, $plugin) {
                $cartRequestTransfer->setCoupon('Coupon for ' . $quoteTransfer->getName());
                return $cartRequestTransfer;
            },
            'cart_note' => 'name',
        ];
    }

    /**
     * @return array
     */
    public function getCustomCartItemMapping(): array
    {
        return[
            //ItemTransfer => PunchoutCatalogDocumentCartItemTransfer

            'tax_description' => function () {
                return 'test';
            },
            /**
             * @param \Generated\Shared\Transfer\ItemTransfer
             * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
             * @param \Generated\Shared\Transfer\QuoteTransfer
             * @param \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperDefaultPlugin
             */
            function ($quoteItemTransfer, $documentCartItemTransfer, $quoteTransfer, $plugin) {
                $name = trim($quoteItemTransfer->getName());
                $documentCartItemTransfer->setDiscountDescription('Custom discount description for ' . $name);
                return $documentCartItemTransfer;
            },
            'discount_description' => 'name',
            'cart_note' => 'group_key',
        ];
    }

    /**
     * @return array
     */
    public function getCustomCartCustomerMapping(): array
    {
        return [
            //CustomerTransfer => PunchoutCatalogDocumentCartCustomerTransfer

            'first_name' => 'customer_reference',

            /**
             * @param \Generated\Shared\Transfer\CustomerTransfer
             * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCustomerTransfer
             * @param \Generated\Shared\Transfer\QuoteTransfer
             * @param \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperDefaultPlugin
             */
            function ($quoteCustomerTransfer, $documentCartCustomerTransfer, $quoteTransfer, $plugin) {
                return $documentCartCustomerTransfer;
            },
        ];
    }
}
```


If this opportunity is not enough, you could define your own plugin that should implement `PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperPluginInterface`
and add it by overriding `PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogDependencyProvider::getCartTransferMapperPlugins` method.


### Enable Controllers in a new way

Register Punchout routes in src/Pyz/Yves/Router/RouterDependencyProvider.php:

```php
<?php

namespace Pyz\Yves\Router;

use PunchoutCatalog\Yves\PunchoutCatalog\Plugin\Router\PunchoutCatalogRouteProviderPlugin;
use Spryker\Yves\Router\RouterDependencyProvider as SprykerRouterDependencyProvider;

class RouterDependencyProvider extends SprykerRouterDependencyProvider
{
    /**
     * @return \Spryker\Yves\RouterExtension\Dependency\Plugin\RouteProviderPluginInterface[]
     */
    protected function getRouteProvider(): array
    {
        return [
            new PunchoutCatalogRouteProviderPlugin()
        ];
    }
}
```
### Enable Controllers in a legacy way

Register Punchout routes in src/Pyz/Yves/ShopApplication/YvesBootstrap.php:

```php
<?php
 
namespace Pyz\Yves\ShopApplication;
 
use PunchoutCatalog\Yves\PunchoutCatalog\Plugin\Provider\PunchoutCatalogControllerProvider;
use SprykerShop\Yves\ShopApplication\YvesBootstrap as SprykerYvesBootstrap;
 
class YvesBootstrap extends SprykerYvesBootstrap
{
    /**
     * @param bool|null $isSsl
     *
     * @return \SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider[]
     */
    protected function getControllerProviderStack($isSsl)
    {
        return [
            new PunchoutCatalogControllerProvider($isSsl),
        ];
    }
}
```
