<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);

$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->addProductToCart('/en/Asus-HDMI-HDMI-215?attribute%5Bpackaging_unit%5D=Ring+%28500m%29');
$i->see('cart');

$quantity = $i->getElement('[data-qa="quantity-input"]')->last()->attr('value');
codecept_debug('Get product quantity from cart page: ' . $quantity);

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<UnitOfMeasure>EA</UnitOfMeasure>');
$i->canSeeCxmlContains($data, "<ItemIn lineNumber=\"1\" quantity=\"$quantity\">");
//@todo: improve this test-case - validate entire product, check for children products and other products in POOM