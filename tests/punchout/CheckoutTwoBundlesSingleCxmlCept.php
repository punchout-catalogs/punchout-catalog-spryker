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
    \Helper\Punchout::BUSINESS_UNIT_USER_2,
    \Helper\Punchout::getCxmlDynamicSetupRequestData('user_2', 'user_2_pass')
);

$i->switchToGrossPrices();

$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_SONY_210);
$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_HP_211);

$i->cartTransfer();

$data = $i->getUrlEncodedCxmlCartResponse();
$i->seeCxml($data);

$i->canSeeCxmlContains($data, '<SupplierPartID>210_123</SupplierPartID>');
$i->canSeeCxmlContains($data, '<SupplierPartID>211_123</SupplierPartID>');

$i->wantTo('check two bundle products exists in cXML Order Message');

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

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
    $i->wantTo('check bundle product SKU: ' . $product['sku']);
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
