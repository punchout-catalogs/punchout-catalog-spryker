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
    \Helper\Punchout::BUSINESS_UNIT_USER_2,
    \Helper\Punchout::getOciSetupRequestData('user_2', 'user_2_pass')
);

$i->switchToGrossPrices();

$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_SONY_210);
$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_HP_211);

$i->cartTransfer();

$i->wantTo('check two bundle products exists in OCI Order Message');

$elements = $i->getOciFormElements();
$tree = $i->toOciElementsTree($elements);

$i->assertNotEmpty($elements);

$products = [
    [
        'idx' => '1',
        'sku' => '210_123',
        'price' => '1000',
        'name' => 'Sony Bundle',
        'quantity' => 1,
        'uom' => 'EA',
        'currency' => 'EUR',
    ],
    [
        'idx' => '2',
        'sku' => '211_123',
        'price' => '705',
        'name' => 'HP Bundle',
        'quantity' => 1,
        'uom' => 'EA',
        'currency' => 'EUR',
    ],
];

foreach ($products as $product) {
    $i->wantTo('assert bundle product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertOciProductItemBundleSingleSpecific($elements[$idx]);
    $i->assertOciProductItem($elements[$idx], $product);
    
    $i->wantTo('check there is not any child product of the product SKU: ' . $product['sku']);
    $i->assertTrue(!isset($tree[$idx]));
}

$i->wantTo('check all products exists in OCI Order Message and all are simple items');
$i->assertNotEmpty($elements);

$skus = array_column($products, 'sku');
$skus = array_merge($skus, \Helper\Punchout::ALL_TOTAL_SKUS);

/** @var array $el */
foreach ($elements as $elIdx => $el) {
    $i->wantTo('check is product common values ' . $elIdx);
    $i->assertNotEmptyOciElementBasicElements($el);
    
    $i->assertEmpty($el['PARENT_ID']);
    $i->assertEmpty($el['ITEM_TYPE']);
    
    $sku = $el['VENDORMAT'];
    $i->wantTo('check if SKU is expected: ' . $sku);
    $i->assertTrue(in_array($sku, $skus));
}
