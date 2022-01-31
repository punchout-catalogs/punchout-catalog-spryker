# PunchOutCatalogs Module

Punchout Catalog Module for Spryker eCommerce Platform

## Important Changes

Since the `2.4.0` version it has been changed the way of punchout connection load.

Is required to enter Sender ID for cXML Setup Request in the following format:
`Credential_Domain_Value/Credential_Identity_Value`.

Example #1:
`AribaNetworkId/AN119990XX`

Example #2:
`NetworkId/NID119990XX`


## Installation

```
composer require punchout-catalogs/punchout-catalog-spryker
```

*B2C* store additionally requires the `spryker-feature/company-account` feature.

Look at `etc/integration-sample/*.patch` patches as example of integration with Spryker [B2B](https://github.com/spryker-shop/b2b-demo-shop)  and [B2C](https://github.com/spryker-shop/b2c-demo-shop) demo stores.

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


### Enable Yves Controllers in a new way (since Spryker version `202001`)

Enable Yves Punchout routes in `src/Pyz/Yves/Router/RouterDependencyProvider.php`:

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

### Enable Yves Controllers in a legacy way (before Spryker version `202001`)

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

### Enable Zed Controllers in a new way (since Spryker version `202001`)

Enable Zed Punchout routes in `src/Pyz/Zed/Router/RouterConfig.php`:

```php
<?php

namespace Pyz\Zed\Router;

use Spryker\Zed\Router\RouterConfig as SprykerRouterConfig;

class RouterConfig extends SprykerRouterConfig
{
    /**
     * @return string[]
     */
    public function getControllerDirectories(): array
    {
        $controllerDirectories = parent::getControllerDirectories();

        //...
        $controllerDirectories[] = sprintf('%s/punchout-catalogs/*/src/*/Zed/*/Communication/Controller/', APPLICATION_VENDOR_DIR);

        return array_filter($controllerDirectories, 'glob');
    }
}
```

## Troubleshooting

### Issue with Auth Token Create Error

The `punchout-catalog.error.auth.token.create` error may happen if the `spy_oauth_access_token.user_identifier` field is too small for data which is storing in the field. By default it is `varchar(1024)`.

Solution:

The easiest way to improve it is upgrading the field from `varchar(1024)` to `LONGVARCHAR`.

Create a scheme file #1: `src/Pyz/Zed/PunchoutCatalog/Persistence/Propel/Schema/spy_oauth.schema.xml`:
```xml
<?xml version="1.0"?>
<database xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          name="zed"
          xsi:noNamespaceSchemaLocation="http://static.spryker.com/schema-01.xsd"
          namespace="Orm\Zed\Oauth\Persistence"
          package="src.Orm.Zed.Oauth.Persistence">

    <table name="spy_oauth_access_token">
        <column name="user_identifier" type="LONGVARCHAR"/>
    </table>

</database>
```

Create a scheme file #2: `src/Pyz/Zed/PunchoutCatalog/Persistence/Propel/Schema/spy_oauth_revoke.schema.xml`:
```xml
<?xml version="1.0"?>
<database xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          name="zed"
          xsi:noNamespaceSchemaLocation="http://static.spryker.com/schema-01.xsd"
          namespace="Orm\Zed\OauthRevoke\Persistence"
          package="src.Orm.Zed.OauthRevoke.Persistence">

    <table name="spy_oauth_refresh_token">
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

And run the `application:build-navigation-cache` command if navigation menu is cached (store runs in `production` mode).


### Example of OCI Cart mapping with many custom fields:

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
                "path": "NEW_ITEM-DESCRIPTION[%line_number%]",
                "transform": [{
                    "cut": {
                        "len": "40"
                    }
                }]
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

### Example of cXML Cart mapping:
```json
{
  "cart": {
    "fields": {
      "grand_total": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Total[1]/Money[1]"
      },

      "tax_total": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Money[1]"
      },
      "tax_description": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Description[1]"
      },

      "discount_total": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Money[1]"
      },
      "discount_description": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Description[1]"
      },

      "currency": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Total[1]/Money[1]/@currency,/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Tax[1]/Money[1]/@currency,/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Discount[1]/Money[1]/@currency",
        "append": true
      },
                  "cart_note": {
        "path": "/cXML/Message[1]/PunchOutOrderMessage[1]/PunchOutOrderMessageHeader[1]/Comments[1]"
      }
    }
  },
  "cart_item": {
    "fields": {
      "line_number": {
        "path": "@lineNumber"
      },
                  "parent_line_number": {
        "path": "@parentLineNumber"
      },
                  "item_type": {
        "path": "@itemType"
      },
                  "composite_item_type": {
        "path": "@compositeItemType"
      },
      "quantity": {
        "path": "@quantity"
      },
      "internal_id": {
        "path": "ItemID[1]/SupplierPartAuxiliaryID[1]"
      },
      "sku": {
        "path": "ItemID[1]/SupplierPartID[1],ItemDetail[1]/BuyerPartID[1],ItemDetail[1]/ManufacturerPartID[1]"
      },
      "unit_total": {
        "path": "ItemDetail[1]/UnitPrice[1]/Money[1]"
      },
      "currency": {
        "path": "ItemDetail[1]/UnitPrice[1]/Money[1]/@currency"
      },
      "name": {
        "path": "ItemDetail[1]/Description[1]/ShortName"
      },
      "long_description": {
        "path": "ItemDetail[1]/Description[1]"
      },
      "uom": {
        "path": "ItemDetail[1]/UnitOfMeasure[1]",
        "transform": [{
          "default": {
            "value": "EA"
          }
        }]
      },
      "brand": {
        "path": "ItemDetail[1]/ManufacturerName[1]"
      },
      "supplier_id": {
        "path": "ItemDetail[1]/SupplierID[1]"
      },
      "cart_note": {
        "path": "ItemDetail[1]/Comments[1]"
      },
      "image_url": {
        "path": "ItemDetail[1]/Extrinsic[@name='ImageURL']"
      },
      "locale": {
        "path": "ItemDetail[1]/Description[1]/@xml:lang"
      },
      "options": {
        "path": "ItemDetail[1]/Extrinsic/customOption()",
        "multiple": true
      }
    }
  },
  "customOption": {
    "fields": {
      "code": {
        "path": "@name"
      },
      "value": {
        "path": "./"
      }
    }
  }
}
```

### Example of cXML Request mapping:
```json
{
  "customer": {
    "fields": {
      "first_name": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='FirstName']"
      },
      "last_name": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='LastName']"
      },
      "email": {
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/Extrinsic[@name='UserEmail']"
      }
    }
  },
  "cart_item": {
    "fields": {
      "internal_id":{
        "path": "/cXML/Request[1]/PunchOutSetupRequest[1]/ItemOut/ItemID[1]/SupplierPartAuxiliaryID"
      }
    }
  }
}
```
