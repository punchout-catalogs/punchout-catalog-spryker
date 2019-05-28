<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;


use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder;

class DecoderTest extends Unit
{
    /**
     * @var Decoder
     */
    protected $decoder;

    /**
     *
     */
    protected function _setUp()
    {
        $this->decoder = new Decoder();
        return parent::_setUp();
    }
}
