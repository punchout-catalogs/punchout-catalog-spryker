<?php
/** @var \Helper\Punchout | PunchoutTester $i */
/** @var \Codeception\Scenario $scenario */
$i = new PunchoutTester($scenario);
$i->haveHttpHeader('content-type', 'text/xml');


$i->wantTo('perform correct cxml setup request with business-unit from another company and see result');

$i->sendPOST('/request?business-unit=16&store=de', \Helper\Punchout::getCxmlDynamicSetupRequestData('user_30', 'user_30_pass'));
$i->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$i->seeResponseIsXml();
$i->canSeeXmlResponseIncludes('<Status code="401" text="Unauthorized">Authentication Failed</Status>');
