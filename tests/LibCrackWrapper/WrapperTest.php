<?php
declare(strict_types=1);
namespace LibCrackWrapper;

use PHPUnit\Framework\TestCase;

class WrapperTest extends TestCase
{
    /** @covers \LibCrackWrapper\Wrapper::checkPassword */
    public function testCheckPassword(): void
    {
        $obj = new Wrapper();
        $this->assertEquals('it is WAY too short', (string) $obj->checkPassword('123'));
    }
}
