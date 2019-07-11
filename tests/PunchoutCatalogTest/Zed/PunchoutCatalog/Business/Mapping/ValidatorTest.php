<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;


use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Validator;

class ValidatorTest extends Unit
{
    /**
     * @var Validator
     */
    protected $validator;

    protected function _setUp(): void
    {
        $this->validator = new Validator();
        parent::_setUp();
    }
}
