<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\StripCommand;

class StripCommandTest extends Unit
{
    /**
     * @var StripCommand
     */
    protected $command;


    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('Test paragraph. Other text', $result);
    }

    public function setUp()
    {
        $this->command = new StripCommand();
        parent::setUp();
    }

}
