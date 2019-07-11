<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AppendCommand;

class AppendCommandTest extends Unit
{
    /**
     * @var AppendCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('_append'));
        $value = 'value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('value_append', $result);
    }

    public function testArray()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setValue('_append'));
        $value = [
            'value1',
            'value2'
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals([
            'value1_append',
            'value2_append',
        ], $result);
    }
    
    public function setUp(): void
    {
        $this->command = new AppendCommand();
        parent::setUp();
    }
}
