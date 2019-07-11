<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getOciSetupRequestData()
);

$i->switchToGrossPrices();

$sku = '218_1234';

$i->addProductToCartWithOptions(
    \Helper\Punchout::PRODUCT_PU_SCREW_218_PACK_GIFTBOX,
    $sku,
    [
        'sales-unit-quantity' => 0.01,
        'quantity' => 2,
        'id-product-measurement-sales-unit' => 21,
        'amount-sales-unit' => [$sku => 1000],
        'amount' => [$sku => 400],
        'amount-id-product-measurement-sales-unit' => [$sku => 12]
    ]
);

$i->see('cart');

$quantity = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->first()->text();
codecept_debug('Get product quantity from cart page: ' . $quantity);

$amount = $i->getElement('.packaging-unit-cart .packaging-unit-cart__value')->last()->text();
codecept_debug('Get product amount from cart page: ' . $amount);

$i->cartTransfer();

$i->wantTo('check two bundle products exists in OCI Order Message');

$elements = $i->getOciFormElements();
$tree = $i->toOciElementsTree($elements);

$i->assertNotEmpty($elements);

$products = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '65',
        'name' => 'Giftbox',
        'quantity' => $quantity,
        'uom' => 'EA',
        'currency' => 'EUR',
    ],
];

foreach ($products as $product) {
    $i->wantTo('assert bundle product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertOciProductItemBundleComplexSpecific($elements[$idx]);
    $i->assertOciProductItem($elements[$idx], $product);
    
    $i->assertTrue(count($tree[$idx]) == 1);
    
    $i->wantTo('check children products of the product SKU: ' . $product['sku']);
    
    foreach ($tree[$idx] as $childIdx => $child) {
        $i->wantTo('assert bundle product SKU: ' . $product['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertNotEmpty($child['PARENT_ID']);
        $i->assertEquals($idx, $child['PARENT_ID']);
        
        $i->assertNotEmpty($child['ITEM_TYPE']);
        $i->assertEquals('O', $child['ITEM_TYPE']);
        
        $i->assertEquals($amount, $child['QUANTITY']);//
        
        $i->assertNotEmptyOciElementBasicElements($child);
    }
}
