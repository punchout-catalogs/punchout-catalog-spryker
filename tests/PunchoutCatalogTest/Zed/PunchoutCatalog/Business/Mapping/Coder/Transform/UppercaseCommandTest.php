<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\UppercaseCommand;

class UppercaseCommandTest extends Unit
{
    /**
     * @var UppercaseCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = 'Some_Value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('SOME_VALUE', $result);
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
            'UPPPER',
            'LOWER',
            'SOMECASE',
            '',
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new UppercaseCommand();
        parent::setUp();
    }
}
