<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');

$i->wantTo('perform wrong setup request format and see result');

$i->sendPOST('/request?business-unit=16&store=de', ['name' => 'test', 'email' => 'test@codeception.com']);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeResponseContains('<Status code="406" text="Not Acceptable">Invalid PunchOut Format</Status>');

$i->wantTo('perform setup request with wrong business-unit and see result');

$i->sendPOST('/request?business-unit=9999&store=de', ['name' => 'test', 'email' => 'test@codeception.com']);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeResponseContains('<Status code="500" text="Internal Server Error">Missed Company Business Unit</Status>');


$i->wantTo('perform correct cxml setup request with wrong identity / secret and see result');

$i->sendPOST('/request?business-unit=16&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('wrong_user', 'wrong_secret'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="401" text="Unauthorized">Authentication Failed</Status>');

$i->wantTo('perform correct cxml setup request with existent user who does not belong to any business unit and see result');

$i->sendPOST('/request?business-unit=13&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('user_30', 'user_30_pass', 'maria.williams@spryker.com'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="500" text="Internal Server Error">Missed Company User</Status>');

$i->wantTo('perform correct cxml setup request with existent user who belongs to several business units and see result');

$i->sendPOST('/request?business-unit=13&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('user_30', 'user_30_pass', 'henry.tudor@spryker.com'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="500" text="Internal Server Error">Customer should have only one Company user to login</Status>');

$i->wantTo('perform correct cxml setup request to inactive connection and see result');

$i->sendPOST('/request?business-unit=13&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('user_20', 'user_20_pass'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="401" text="Unauthorized">Authentication Failed</Status>');

$i->wantTo('perform correct cxml setup request to connection with empty customer mapping, dynamic strategy and see result');

$i->sendPOST('/request?business-unit=16&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('user_10', 'user_10_pass'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="500" text="Internal Server Error">Missed Company User</Status>');
