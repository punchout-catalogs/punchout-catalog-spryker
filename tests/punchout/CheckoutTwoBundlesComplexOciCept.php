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
    $i->assertOciProductItemBundleComplexSpecific($elements[$idx]);
    $i->assertOciProductItem($elements[$idx], $product);
    
    $i->wantTo('check children products of the product SKU: ' . $product['sku']);
    
    $i->assertNotEmpty($tree[$idx]);
    
    foreach ($tree[$idx] as $childIdx => $child) {
        $i->wantTo('assert bundle product SKU: ' . $product['sku'] . ' child SKU #' . $childIdx);
        $i->assertNotEmptyOciElementBasicElements($child);
        
        $i->assertNotEmpty($child['PARENT_ID']);
        $i->assertEquals($idx, $child['PARENT_ID']);
        
        $i->assertNotEmpty($child['ITEM_TYPE']);
        $i->assertEquals('O', $child['ITEM_TYPE']);
    }
}
