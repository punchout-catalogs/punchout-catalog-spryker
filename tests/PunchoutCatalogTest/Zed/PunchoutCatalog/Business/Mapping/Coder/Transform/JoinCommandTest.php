<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\JoinCommand;

class JoinCommandTest extends Unit
{
    /**
     * @var JoinCommand
     */
    protected $command;

    public function testExecute()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = [
            'a',
            'b',
            'c',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('a, b, c', $result);
    }

    public function testCustomSeparator()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $transform->setParams((new PunchoutCatalogMappingTransformParamsTransfer())->setSep(';'));
        $value = [
            'a',
            'b',
            'c',
        ];
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('a;b;c', $result);
    }

    public function testSongleValue()
    {
        $transform = new PunchoutCatalogMappingTransformTransfer();
        $value = 'value';
        $result = $this->command->execute($transform, $value);
        $this->assertEquals('value', $result);
    }
    
    public function setUp(): void
    {
        $this->command = new JoinCommand();
        parent::setUp();
    }
}
