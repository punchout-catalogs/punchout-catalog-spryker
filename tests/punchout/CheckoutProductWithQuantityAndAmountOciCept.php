<?php
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

$bundles = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '65',
        'name' => 'Giftbox',
        'quantity' => $quantity,
    ],
];

foreach ($bundles as $bundle) {
    $i->wantTo('assert bundle product SKU: ' . $bundle['sku']);
    $idx = $bundle['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertNotEmptyOciElementBasicElements($elements[$idx]);
    
    $i->assertEmpty($elements[$idx]['PARENT_ID']);
    
    $i->assertNotEmpty($elements[$idx]['ITEM_TYPE']);
    $i->assertEquals('R', $elements[$idx]['ITEM_TYPE']);
    
    $i->assertEquals($bundle['quantity'], $elements[$idx]['QUANTITY']);//
    $i->assertEquals($bundle['sku'], $elements[$idx]['VENDORMAT']);//
    $i->assertEquals($bundle['name'], $elements[$idx]['DESCRIPTION']);//
    $i->assertEquals($bundle['price'], $elements[$idx]['PRICE']);//
    $i->assertEquals('EA', $elements[$idx]['UNIT']);
    
    $i->wantTo('check children products of the product SKU: ' . $bundle['sku']);
    
    $i->assertNotEmpty($tree[$idx]);
    
    foreach ($tree[$idx] as $childIdx => $child) {
        $i->wantTo('assert bundle product SKU: ' . $bundle['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertNotEmpty($child['PARENT_ID']);
        $i->assertEquals($idx, $child['PARENT_ID']);
        
        $i->assertNotEmpty($child['ITEM_TYPE']);
        $i->assertEquals('O', $child['ITEM_TYPE']);
        
        $i->assertEquals($amount, $child['QUANTITY']);//
        
        $i->assertNotEmptyOciElementBasicElements($child);
    }
}