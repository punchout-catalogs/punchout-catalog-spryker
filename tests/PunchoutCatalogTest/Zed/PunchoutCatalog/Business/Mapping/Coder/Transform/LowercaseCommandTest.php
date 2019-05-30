<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\LowercaseCommand;

class LowercaseCommandTest extends Unit
{
    /**
     * @var LowercaseCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = 'Some_Value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('some_value', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = [
            'UPPPER',
            'lower',
            'SomeCase',
            '',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'uppper',
            'lower',
            'somecase',
            '',
        ], $result);
    }

    public function setUp()
    {
        $this->command = new LowercaseCommand();
        parent::setUp();
    }

}
