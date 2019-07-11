<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');


$i->wantTo('perform correct cxml setup request and see result');
$i->setupRequestCxmlGetUrl(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);


$i->wantTo('perform correct cxml setup request again with same user and see result');
$i->setupRequestCxml(
    \Helper\Punchout::BUSINESS_UNIT_USER_1,
    \Helper\Punchout::getCxmlDynamicSetupRequestData()
);

$i->addProductToCart(\Helper\Punchout::PRODUCT_SIMPLE_CANON_POWERSHOT_35);
$i->see('cart');

$price = $i->getElement('[data-qa="component cart-item-summary"] .list__item .float-right')->last()->text();
$price = trim($price, '€');
codecept_debug('Get product price from cart page: ' . $price);

$i->cartTransfer();

$data = $i->getBase64CxmlCartResponse();
$i->seeCxml($data);
$i->canSeeCxmlContains($data, '<ShortName>Canon PowerShot N</ShortName>');
$i->canSeeCxmlContains($data, '<Money currency="EUR">' . $price . '</Money>');
$i->canSeeCxmlContains($data, '<UnitOfMeasure>EA</UnitOfMeasure>');
