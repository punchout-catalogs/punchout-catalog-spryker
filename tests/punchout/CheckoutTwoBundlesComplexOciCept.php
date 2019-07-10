<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct oci setup request and see result');
$i->setupRequestOci(
    \Helper\Punchout::getOciSetupRequestData()
);

$i->switchToGrossPrices();
$i->addToCartBundleProductSony210();
$i->addToCartBundleProductHp211();
$i->cartTransfer();

$i->wantTo('assert bundle product attributes');

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
    $i->assertEmpty($elements[$idx]['PARENT_ID']);
    
    $i->assertNotEmpty($elements[$idx]['ITEM_TYPE']);
    $i->assertEquals('R', $elements[$idx]['ITEM_TYPE']);
    
    $i->assertNotEmpty($elements[$idx]['QUANTITY']);
    $i->assertEquals('1', $elements[$idx]['QUANTITY']);
    
    $i->assertNotEmpty($elements[$idx]['VENDORMAT']);
    $i->assertEquals($bundle['sku'], $elements[$idx]['VENDORMAT']);
    
    $i->assertNotEmpty($elements[$idx]['DESCRIPTION']);
    $i->assertEquals($bundle['name'], $elements[$idx]['DESCRIPTION']);
    
    $i->assertNotEmpty($elements[$idx]['PRICE']);
    $i->assertEquals($bundle['price'], $elements[$idx]['PRICE']);
    
    $i->assertNotEmpty($elements[$idx]['UNIT']);
    $i->assertEquals('EA', $elements[$idx]['UNIT']);
    
    $i->assertNotEmpty($elements[$idx]['EXT_PRODUCT_ID']);
    $i->assertNotEmpty($elements[$idx]['CURRENCY']);
    $i->assertNotEmpty($elements[$idx]['LONGTEXT']);
    $i->assertNotEmpty($elements[$idx]['VENDOR']);
    
    $i->assertNotEmpty($tree[$idx]);
    $children = $tree[$idx];
    foreach ($children as $child) {
        $i->assertNotEmpty($child['PARENT_ID']);
        $i->assertEquals($idx, $child['PARENT_ID']);
        
        $i->assertNotEmpty($child['ITEM_TYPE']);
        $i->assertEquals('O', $child['ITEM_TYPE']);
    
        $i->assertNotEmpty($child['QUANTITY']);
        $i->assertNotEmpty($child['VENDORMAT']);
        $i->assertNotEmpty($child['DESCRIPTION']);
        $i->assertNotEmpty($child['PRICE']);
        $i->assertNotEmpty($child['UNIT']);
        $i->assertNotEmpty($child['EXT_PRODUCT_ID']);
        $i->assertNotEmpty($child['CURRENCY']);
        $i->assertNotEmpty($child['LONGTEXT']);
        $i->assertNotEmpty($child['VENDOR']);
    }
}
