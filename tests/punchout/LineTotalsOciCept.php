<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);


$i->wantTo('perform correct oci setup request and see result');

$ociSetupRequestData = \Helper\Punchout::getOciSetupRequestData();
$i->sendPOST('/request?business-unit=16&store=de', $ociSetupRequestData);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

$yvesUrl = $i->getAccessUrlFromOci();
$i->canSeeCorrectAccessUrl($yvesUrl);


$i->wantTo('Login by access url');

$i->amOnUrl($yvesUrl);
$i->seeCurrentUrlEquals('/en');


$i->wantTo('Add product to cart');

$i->amOnPage('/en/canon-powershot-n-35');
$i->click('[id="add-to-cart-button"]');
$i->see('cart');


$i->wantTo('Transfer cart');

$i->stopFollowingRedirects();
$i->click('[data-qa="punchout-catalog.cart.go-to-transfer"]');
$i->seeCurrentUrlEquals('/en/punchout-catalog/cart/transfer');

$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[2]',
    'value' => 'Estimated Tax',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[2]',
    'value' => '42.75',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[2]',
    'value' => 'EUR',
]);

$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-DESCRIPTION[3]',
    'value' => 'Estimated Discount',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[3]',
    'value' => '-29.75',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-CURRENCY[3]',
    'value' => 'EUR',
]);
