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

$sku = '193_32124735';

$quantity = 5;

$i->addProductToCartWithOptions(
    \Helper\Punchout::PRODUCT_SONY_FDR_AX40,
    $sku,
    [
        'quantity' => $quantity,
    ]
);

$i->see('cart');

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);

$xml = simplexml_load_string($data);
$i->assertTrue($xml instanceof \SimpleXMLElement);

$products = [
    [
        'idx' => '1',
        'sku' => $sku,
        'price' => '1.65',
        'name' => 'Sony FDR-AX40',
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
    $i->canNotSeeCxmlContains($data, 'parentLineNumber="' . $lineNumber . '" itemType="item"');

    $i->wantTo('check there is not any child product of the product SKU: ' . $product['sku']);
    $childrenXpath = sprintf('/cXML/Message/PunchOutOrderMessage/ItemIn[@parentLineNumber="%s"]', $lineNumber);
    $children = $xml->xpath($childrenXpath);
    $i->assertEmpty($children);
}
