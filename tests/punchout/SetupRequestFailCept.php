<?php
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');
$i->wantTo('perform wrong setup request format and see result');
$i->sendPOST('/request?business-unit=16', ['name' => 'test', 'email' => 'test@codeception.com']);
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeResponseContains('<Status code="406" text="Not Acceptable">Invalid PunchOut Format</Status>');
