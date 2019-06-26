<?php
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
