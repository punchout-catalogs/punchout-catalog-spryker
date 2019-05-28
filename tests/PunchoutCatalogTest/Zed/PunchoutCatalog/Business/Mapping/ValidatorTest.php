<?php

namespace PunchoutCatalogTest\Zed\PunchoutCatalog\Business\Mapping;


use Codeception\Test\Unit;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Validator;

class ValidatorTest extends Unit
{
    /**
     * @var Validator
     */
    protected $validator;

    protected function _setUp()
    {
        $this->validator = new Validator();
        return parent::_setUp();
    }


}
