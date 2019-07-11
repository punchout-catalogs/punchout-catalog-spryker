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
$i->addProductToCart(\Helper\Punchout::PRODUCT_PU_ASUS_HDMI_217_PACK_RING_500);
$i->see('cart');

$quantity = $i->getElement('[data-qa="quantity-input"]')->last()->attr('value');
codecept_debug('Get product quantity from cart page: ' . $quantity);

$i->cartTransfer();

$elements = $i->getOciFormElements();
$tree = $i->toOciElementsTree($elements);

$i->assertNotEmpty($elements);

$products = [
    [
        'idx' => '1',
        'sku' => '215_124',
        'price' => '12.5',
        'name' => 'ASUS HDMI-HDMI Red',
        'currency' => 'EUR',
        'quantity' => $quantity,
        'uom' => 'EA',
    ],
];

foreach ($products as $product) {
    $i->wantTo('check product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertNotEmptyOciElementBasicElements($elements[$idx]);
    
    $i->assertEmpty($elements[$idx]['PARENT_ID']);
    $i->assertEmpty($elements[$idx]['ITEM_TYPE']);
    
    $i->assertEquals($product['quantity'], $elements[$idx]['QUANTITY']);
    $i->assertEquals($product['sku'], $elements[$idx]['VENDORMAT']);
    $i->assertEquals($product['name'], $elements[$idx]['DESCRIPTION']);
    $i->assertEquals($product['price'], $elements[$idx]['PRICE']);
    $i->assertEquals($product['uom'], $elements[$idx]['UNIT']);
    $i->assertEquals($product['currency'], $elements[$idx]['CURRENCY']);
    
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
