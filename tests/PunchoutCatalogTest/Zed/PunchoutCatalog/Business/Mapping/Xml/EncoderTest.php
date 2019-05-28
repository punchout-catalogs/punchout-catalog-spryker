<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder;

class EncoderTest extends Unit
{
    use Helper;
    /**
     * @var Encoder
     */
    protected $encoder;

    public function testExecute()
    {
        $mapping = $this->createMapping();

        $transferData = new PunchoutCatalogCartRequestTransfer();
        $document = $this->encoder->execute($mapping, $transferData);

//        codecept_debug($document);
        $this->assertInstanceOf(\SimpleXMLElement::class, $document, 'Should be the xml document');
    }

    /**
     *
     */
    public function setUp()
    {
        $this->encoder = new Encoder();
        return parent::setUp();
    }
}
