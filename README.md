# PunchOutCatalog Module
[![Build Status](https://travis-ci.org/punchout-catalogs/punchout-catalog-spryker.svg)](https://travis-ci.org/punchout-catalogs/punchout-catalog-spryker)
[![Coverage Status](https://coveralls.io/repos/github/punchout-catalogs/punchout-catalog-spryker/badge.svg)](https://coveralls.io/github/punchout-catalogs/punchout-catalog-spryker)

{{ADD DESCRIPTION HERE}}

## Installation

```
composer require punchout-catalogs/punchout-catalog-spryker
```

## Documentation

[Spryker Documentation](https://academy.spryker.com/developing_with_spryker/module_guide/modules.html)



### Custom cart mapping

Extending cart mapping behavior could be implemented by adding custom mapping to application configuration:

```php
// QuoteTransfer => PunchoutCatalogDocumentCartTransfer
$config[PunchoutCatalogConstants::CUSTOM_CART_TRANSFER_MAPPING] = [
    /**
     * Closure without key in array should return transfer object 
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperDefaultPlugin
     */
    function ($quoteTransfer, $cartRequestTransfer, $plugin) {
        $cartRequestTransfer->setCoupon('Coupon for ' . $quoteTransfer->getName());
        return $cartRequestTransfer;
    },
    
    'cart_note' => 'name',
];

// ItemTransfer => PunchoutCatalogDocumentCartItemTransfer
$config[PunchoutCatalogConstants::CUSTOM_CART_ITEM_TRANSFER_MAPPING] = [
    /**
     * Closure with key in array should return field value
     *
     * @param \Generated\Shared\Transfer\ItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperDefaultPlugin
     */
    'tax_description' => function () {
        return 'test description';
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

// CustomerTransfer => PunchoutCatalogDocumentCartCustomerTransfer
$config[PunchoutCatalogConstants::CUSTOM_CART_CUSTOMER_TRANSFER_MAPPING] = [
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
```


If this opportunity is not enough, you could define your own plugin that should implement `PunchoutCatalog\Yves\PunchoutCatalog\Mapper\CartTransferMapperPluginInterface`
and add it by overriding `PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogDependencyProvider::getCartTransferMapperPlugins` method.
