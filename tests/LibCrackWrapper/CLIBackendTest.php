<?php

declare(strict_types=1);

namespace LibCrackWrapper;

use RuntimeException;

final class CLIBackendTest extends WrapperTest
{
    static public function setUpBeforeClass(): void
    {
        CLIBackendTest::$wrapper = new Wrapper(Wrapper::BACKEND_CLI);
    }

    /**
     * @covers \LibCrackWrapper\Wrapper::checkUserAndPassword
     */
    public function testCheckUserAndPassword(): void
    {
        $this->expectException(RuntimeException::class);
        CLIBackendTest::$wrapper->checkUserAndPassword('', '', '');
    }

    /** @covers \LibCrackWrapper\Wrapper::getBackendName */
    public function testGetBackendName(): void
    {
        $this->assertSame(CLIBackendTest::$wrapper->getBackendName(), 'CLI');
    }
}
