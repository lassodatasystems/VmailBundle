<?php

namespace Lasso\VmailBundle\Tests;

use Lasso\VmailBundle\Entity\Alias;
use PHPUnit_Framework_TestCase;

class AliasTest extends PHPUnit_Framework_TestCase{

    use AccessorTest;

    /**
     * @test
     */
    public function accessors()
    {
        $alias = new Alias();
        $this->accessorTest($alias);
    }
}
