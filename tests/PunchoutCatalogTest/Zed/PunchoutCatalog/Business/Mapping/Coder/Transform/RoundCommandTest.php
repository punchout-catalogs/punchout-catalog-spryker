<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\RoundCommand;

class RoundCommandTest extends Unit
{
    /**
     * @var RoundCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '21444.19';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals(21444, $result);
    }

    public function testCustomPrecision()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setPrecision('1'));
        $value = '21444.197';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals(21444.2, $result);
    }
    
    public function setUp(): void
    {
        $this->command = new RoundCommand();
        parent::setUp();
    }
}
