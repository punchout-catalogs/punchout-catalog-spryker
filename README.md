# PunchOutCatalogs Module

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
        return [
            //ItemTransfer => PunchoutCatalogDocumentCartItemTransfer
            'custom_sku' => function($quoteItemTransfer, $documentCartItemTransfer, $quoteTransfer, $plugin) {
                return 'here-is-custom-sku-' . $quoteItemTransfer->getAbstractSku();
            },

            'sale_bunch_quantity' => function($quoteItemTransfer, $documentCartItemTransfer, $quoteTransfer, $plugin)  {
                //Product #1
                if ($quoteItemTransfer->getAbstractSku() === 'any_condition_1') {
                    return 100;
                }
                //Product #2
                if ($quoteItemTransfer->getAbstractSku() === 'any_condition_2') {
                    return  50;
                }
                return 1;
            },

            'custom_fields' => function($quoteItemTransfer, $documentCartItemTransfer, $quoteTransfer, $plugin) {
                return array(
                    'custom_field_1' => 'quote-item-id=' . $quoteItemTransfer->getId(),
                    'custom_field_2' => 'custom-abstract-sku-' . $quoteItemTransfer->getAbstractSku(),
                    'custom_field_3' => 'custom_field_value_3',
                    'custom_field_4' => 'custom_field_value_4_' . uniqid(),
                    'custom_field_5' => 'custom_field_value_5_' . uniqid(),
                    'custom_field_contract' => 'ContractID-'. uniqid(),
                    'custom_field_org' => 'TestPurchOrg',
                    'custom_field_ref' => 'some-ref',
                    //...add as many custom fields as you need and can use in mapping
                );
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


### Enable Controllers in a new way (since Spryker version `202001`)

Register Punchout routes in `src/Pyz/Yves/Router/RouterDependencyProvider.php`:

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

Register Punchout routes in `src/Pyz/Yves/ShopApplication/YvesBootstrap.php`:

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

### Example of OCI mapping with many custom fields:

```json
{
    "cart_item": {
        "fields": {
            "quantity": {
                "path": "NEW_ITEM-QUANTITY[%line_number%]"
            },
            "internal_id": {
                "path": "NEW_ITEM-EXT_PRODUCT_ID[%line_number%]"
            },
            "parent_line_number": {
                "path": "NEW_ITEM-PARENT_ID[%line_number%]"
            },
            "item_type": {
                "path": "NEW_ITEM-ITEM_TYPE[%line_number%]",
                "transform":
                [
                    {
                        "map": {
                            "value": "composite",
                            "result": "R"
                        }
                    },
                    {
                        "map": {
                            "value": "item",
                            "result": "O"
                        }
                    }
                ]
            },
            "sku": {
                "path": "NEW_ITEM-VENDORMAT[%line_number%],NEW_ITEM-MANUFACTMAT[%line_number%]"
            },
            "currency": {
                "path": "NEW_ITEM-CURRENCY[%line_number%]"
            },
            "unit_total": {
                "path": "NEW_ITEM-PRICE[%line_number%]"
            },
            "name": {
                "path": "NEW_ITEM-DESCRIPTION[%line_number%]"
            },
            "long_description": {
                "path": "NEW_ITEM-LONGTEXT_%line_number%:132[]"
            },
            "uom": {
                "path": "NEW_ITEM-UNIT[%line_number%]",
                "transform": [{
                    "default": {
                        "value": "EA"
                    }
                }]
            },
            "unspsc": {
                "path": "NEW_ITEM-MATGROUP[%line_number%]"
            },
            "supplier_id": {
                "path": "NEW_ITEM-VENDOR[%line_number%]"
            },
            "sale_bunch_quantity": {
                "path": "NEW_ITEM-PRICEUNIT[%line_number%]"
            },
            "custom_fields/custom_field_org": {
                "path": "NEW_ITEM-PURCHORG[%line_number%]"
            },
            "custom_fields/custom_field_ref": {
                "path": "NEW_ITEM-PURCHINFREC[%line_number%]",
                "transform": [
                    "uppercase"
                ]
            },
            "custom_fields/custom_field_contract": {
                "path": "NEW_ITEM-CONTRACT[%line_number%]",
                "transform": [
                    "lowercase"
                ]
            },
            "custom_fields/custom_field_1": {
                "path": "NEW_ITEM-CUSTFIELD1[%line_number%]"
            },
            "custom_fields/custom_field_2": {
                "path": "NEW_ITEM-CUSTFIELD2[%line_number%]"
            },
            "custom_fields/custom_field_3": {
                "path": "NEW_ITEM-CUSTFIELD3[%line_number%]"
            },
            "custom_fields/custom_field_4": {
                "path": "NEW_ITEM-CUSTFIELD4[%line_number%]"
            },
            "custom_fields/custom_field_5": {
                "path": "NEW_ITEM-CUSTFIELD5[%line_number%]"
            }
        }
    }
}
```

## Troubleshooting

### Issue with Auth Token Create Error

The `punchout-catalog.error.auth.token.create` error may happen if the `spy_oauth_access_token.user_identifier` field is too small for data which is storing in the field. By default it is `varchar(1024)`.

Solution:

The easiest way to improve it is upgrading the field from `varchar(1024)` to `LONGVARCHAR`.

Create a scheme file `src/Pyz/Zed/PunchoutCatalog/Persistence/Propel/Schema/spy_oauth.schema.xml`:
```xml
<?xml version="1.0"?>
<database xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" name="zed" xsi:noNamespaceSchemaLocation="http://static.spryker.com/schema-01.xsd" namespace="Orm\Zed\Oauth\Persistence" package="src.Orm.Zed.Oauth.Persistence">
    <table name="spy_oauth_access_token">
        <column name="user_identifier" type="LONGVARCHAR"/>
    </table>
</database>
```

DB upgrade:

`vendor/bin/console propel:install`

[Database Schema Definition](https://documentation.spryker.com/docs/database-schema-definition)


### Issue with disappeared `PunchOut` menu item in admin panel ( related to [`spryker-eco/punchout-catalogs`](https://github.com/spryker-eco/punchout-catalogs) )

Possible Reason:

Using `BREADCRUMB_MERGE_STRATEGY` hides all custom menu items which are not defined in the `config/Zed/navigation.xml` file.
See: https://docs.spryker.com/docs/scos/dev/back-end-development/extending-spryker/adding-navigation-in-the-back-office.html#defining-a-navigation-merge-strategy
Strategy defined in the `src/Pyz/Zed/ZedNavigation/ZedNavigationConfig.php` file.

Solution:

Restore menu items for `BREADCRUMB_MERGE_STRATEGY` easily by adding the following code to the `config/Zed/navigation.xml` file:

```
    <punchout-catalogs>
        <label>PunchOut</label>
        <title>PunchOut</title>
        <pages>
            <connection>
                <label>Connections</label>
                <title>Connections</title>
                <bundle>punchout-catalogs</bundle>
                <controller>index</controller>
                <action>index</action>
                <visible>1</visible>
            </connection>
            <transaction-log>
                <label>Transactions Log</label>
                <title>Transactions Log</title>
                <bundle>punchout-catalogs</bundle>
                <controller>transaction</controller>
                <action>index</action>
                <visible>1</visible>
            </transaction-log>
        </pages>
    </punchout-catalogs>
```
