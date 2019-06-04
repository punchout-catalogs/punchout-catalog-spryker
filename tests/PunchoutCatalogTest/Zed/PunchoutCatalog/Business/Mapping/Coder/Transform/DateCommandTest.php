<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DateCommand;

class DateCommandTest extends Unit
{
    /**
     * @var DateCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = '01.01.2010';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('2010-01-01 00:00:00', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = [
            '01.01.2010',
            '02.03.99 23:56'
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            '2010-01-01 00:00:00',
            '1999-03-02 23:56:00',
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new DateCommand();
        parent::setUp();
    }
}
