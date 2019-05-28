<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping\Xml;


use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Encoder;

class EncoderTest extends Unit
{
    /**
     * @var Encoder
     */
    protected $encoder;

    /**
     *
     */
    protected function _setUp()
    {
        $this->encoder = new Encoder();
        return parent::_setUp();
    }
}
