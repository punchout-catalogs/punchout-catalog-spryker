<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::BUSINESS_UNIT_USER_2,
    \Helper\Punchout::getOciSetupRequestData('user_2', 'user_2_pass')
);

$i->switchToGrossPrices();
$i->addToCartBundleProductSony210();
$i->addToCartBundleProductHp211();
$i->cartTransfer();

$i->wantTo('check two bundle products exists in OCI Order Message');

$elements = $i->getOciFormElements();
$tree = $i->toOciElementsTree($elements);

$i->assertNotEmpty($elements);

$bundles = [
    [
        'idx' => '1',
        'sku' => '210_123',
        'price' => '1000',
        'name' => 'Sony Bundle',
    ],
    [
        'idx' => '2',
        'sku' => '211_123',
        'price' => '705',
        'name' => 'HP Bundle',
    ],
];

foreach ($bundles as $bundle) {
    $i->wantTo('assert bundle product SKU: ' . $bundle['sku']);
    $idx = $bundle['idx'];
    
    $i->assertNotEmpty($elements[$idx]);
    $i->assertNotEmptyOciElementBasicElements($elements[$idx]);
    
    $i->assertEmpty($elements[$idx]['PARENT_ID']);
    $i->assertEmpty($elements[$idx]['ITEM_TYPE']);
    
    $i->assertEquals('1', $elements[$idx]['QUANTITY']);
    $i->assertEquals($bundle['sku'], $elements[$idx]['VENDORMAT']);
    $i->assertEquals($bundle['name'], $elements[$idx]['DESCRIPTION']);
    $i->assertEquals($bundle['price'], $elements[$idx]['PRICE']);
    $i->assertEquals('EA', $elements[$idx]['UNIT']);
    
    $i->wantTo('check there is not any child product of the product SKU: ' . $bundle['sku']);
    $i->assertTrue(!isset($tree[$idx]));
}

$i->wantTo('check all products exists in OCI Order Message and all are simple items');
$i->assertNotEmpty($elements);

$skus = array_column($bundles, 'sku');
$skus[] = 'tax';
$skus[] = 'discount';
$skus[] = 'expense';

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
