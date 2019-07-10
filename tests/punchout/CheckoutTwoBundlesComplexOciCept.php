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
    
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-PARENT_ID[$idx]",
        'value' => '',
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-ITEM_TYPE[$idx]",
        'value' => 'R',
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-EXT_PRODUCT_ID[$idx]",
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-QUANTITY[$idx]",
        'value' => '1',
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-VENDORMAT[$idx]",
        'value' => $bundle['sku'],
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-PRICE[$idx]",
        'value' => $bundle['price'],
    ]);
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-DESCRIPTION[$idx]",
        'value' => $bundle['name'],
    ]);
    /**
    $i->canSeeElement('input', [
        'name' => "NEW_ITEM-PARENT_ID[\d]",
        'value' => $idx,
    ]);
     */
}
