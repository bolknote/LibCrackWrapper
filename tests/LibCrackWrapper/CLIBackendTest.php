<?php

declare(strict_types=1);

namespace LibCrackWrapper;

final class CLIBackendTest extends WrapperTest
{
    static public function setUpBeforeClass(): void
    {
        CLIBackendTest::$wrapper = new Wrapper(Wrapper::BACKEND_CLI);
    }
}
