<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountFormattedCommand;

class AmountFormattedCommandTest extends Unit
{
    /**
     * @var AmountFormattedCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '21,444.194';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('21444.19', $result);
    }

    public function testCustomFormat()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setThousandsSep('_'));
        $value = '22_444.1944';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('22444.19', $result);
    }

    public function setUp()
    {
        $this->command = new AmountFormattedCommand();
        parent::setUp();
    }

}
