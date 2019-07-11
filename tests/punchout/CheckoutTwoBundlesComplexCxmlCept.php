<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->switchToGrossPrices();

$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_SONY_210);
$i->addProductToCart(\Helper\Punchout::PRODUCT_BUNDLE_HP_211);

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
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
    $i->wantTo('assert bundle product SKU: ' . $product['sku']);
    $idx = $product['idx'];
    
    /** @var \SimpleXMLElement $el */
    $el = $i->getCxmlItemBySku($xml, $product['sku']);
    
    $i->assertNotEmpty($el);
    $i->assertCxmlProductItem($el, $product);
    $i->assertCxmlProductItemBundleComplexSpecific($el);
    
    $lineNumber = $i->getAttributeValue($el, 'lineNumber');
    $i->canSeeCxmlContains($data, 'parentLineNumber="'.$lineNumber.'" itemType="item"');
    
    $children = $i->getCxmlItemsByParentLineNumber($xml, $lineNumber);
    $i->assertNotEmpty($children);
    
    $i->wantTo('check children products of the product SKU: ' . $product['sku']);
    
    /** @var \SimpleXMLElement $childEl */
    foreach ($children as $childIdx => $childEl) {
        $i->wantTo('assert bundle product SKU: ' . $product['sku'] . ' child SKU #' . $childIdx);
        
        $i->assertNotEmpty($i->getAttributeValue($childEl, 'quantity'));
        $i->assertEquals('item', $i->getAttributeValue($childEl, 'itemType'));
        $i->assertNotEmptyCxmlElementBasicElements($childEl);
    }
}
