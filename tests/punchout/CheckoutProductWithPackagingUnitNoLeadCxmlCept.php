<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$sku = '218_1233';
$quantity = 2;

$i->switchToGrossPrices();

$i->addProductToCartWithOptions(
    \Helper\Punchout::PRODUCT_PU_SCREW_218_PACK_MIXED,
    $sku,
    [
        'quantity' => $quantity,
    ]
);

$i->cartTransfer();
$data = $i->getBase64CxmlCartResponse();

$i->seeCxml($data);

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$products = [
    [
        'idx' => '1',
        'sku' => '218_1233',
        'price' => '32.5',
        'name' => 'Mixed Screws boxes',
        'currency' => 'EUR',
        'quantity' => $quantity,
        'uom' => 'EA',
    ],
];

foreach ($products as $product) {
    $i->wantTo('check product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    /** @var \SimpleXMLElement $el */
    $el = $i->getCxmlItemBySku($xml, $product['sku']);
    
    $i->assertNotEmpty($el);
    $i->assertCxmlProductItem($el, $product);
    $i->assertCxmlProductItemBundleSingleSpecific($el);
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canNotSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $i->wantTo('check there is not any child product of the product SKU: ' . $product['sku']);
    $children = $i->getCxmlItemsByParentLineNumber($xml, $lineNumber);
    $i->assertEmpty($children);
}

$i->wantTo('check all products exists in cXML Order Message and all are simple items');

$xpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn');
$elements = $xml->xpath($xpath);
$i->assertNotEmpty($elements);

$skus = array_column($products, 'sku');
$skus = array_merge($skus, \Helper\Punchout::ALL_TOTAL_SKUS);
$i->assertCxmlSkus($elements, $skus);
