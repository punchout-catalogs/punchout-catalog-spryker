<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder;
use SimpleXMLElement;

class AbstractCoderTest extends Unit
{
    /**
     * @var Decoder
     */
    protected $decoder;

    public function testCast()
    {
        $this->assertEquals('1234', $this->decoder->cast(1234), 'Cast for number');
        $this->assertEquals('', $this->decoder->cast(null), 'Cast for null');
        $this->assertEquals('string', $this->decoder->cast('string'), 'Cast for string');
        $element = new SimpleXMLElement('<tag>xml_data</tag>');
        $this->assertEquals('xml_data', $this->decoder->cast($element), 'Cast for SimpleXMLElement');
    }

    /**
     *
     */
    public function setUp()
    {
        $this->decoder = new Decoder();
        return parent::setUp();
    }

}
