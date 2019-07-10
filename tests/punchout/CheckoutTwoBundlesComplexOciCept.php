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
    
    $i->assertNotEmpty($elements[$idx]['ITEM_TYPE']);
    $i->assertEquals('R', $elements[$idx]['ITEM_TYPE']);
    
    $i->assertEquals('1', $elements[$idx]['QUANTITY']);
    $i->assertEquals($bundle['sku'], $elements[$idx]['VENDORMAT']);
    $i->assertEquals($bundle['name'], $elements[$idx]['DESCRIPTION']);
    $i->assertEquals($bundle['price'], $elements[$idx]['PRICE']);
    $i->assertEquals('EA', $elements[$idx]['UNIT']);
    
    $i->wantTo('check children products of the product SKU: ' . $bundle['sku']);
    
    $i->assertNotEmpty($tree[$idx]);
    
    foreach ($tree[$idx] as $childIdx => $child) {
        $i->wantTo('assert bundle product SKU: ' . $bundle['sku'] . ' child SKU #' . $childIdx);
        $i->assertNotEmptyOciElementBasicElements($child);
        
        $i->assertNotEmpty($child['PARENT_ID']);
        $i->assertEquals($idx, $child['PARENT_ID']);
        
        $i->assertNotEmpty($child['ITEM_TYPE']);
        $i->assertEquals('O', $child['ITEM_TYPE']);
    }
}
