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


$i->wantTo('perform correct oci setup request again with same user and see result');

$ociSetupRequestData = \Helper\Punchout::getOciSetupRequestData();
$i->sendPOST('/request?business-unit=16&store=de', $ociSetupRequestData);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$yvesUrl2 = $i->getAccessUrlFromOci();
$i->canSeeCorrectAccessUrl($yvesUrl2);


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
    'name' => 'NEW_ITEM-DESCRIPTION[1]',
    'value' => 'Canon PowerShot N',
]);
$i->canSeeElement('input', [
    'name' => 'NEW_ITEM-PRICE[1]',
    'value' => '267.72',
]);
