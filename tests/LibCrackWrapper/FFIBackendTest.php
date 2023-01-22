<?php

declare(strict_types=1);

namespace LibCrackWrapper;

final class FFIBackendTest extends WrapperTest
{
    /**
     * @return void
     * @requires extension ffi
     */
    static public function setUpBeforeClass(): void
    {
        FFIBackendTest::$wrapper = new Wrapper(Wrapper::BACKEND_FFI);
    }

    /**
     * @covers \LibCrackWrapper\Wrapper::checkUserAndPassword
     * @dataProvider checkingkUserAndPasswordProvider
     */
    public function testCheckUserAndPassword(
        string $pass,
        string $user,
        string $userinfo,
        string $message,
        int $code,
    ): void
    {
        $result = FFIBackendTest::$wrapper->checkUserAndPassword($pass, $user, $userinfo);

        $this->assertSame((string) $result, $message);
        $this->assertSame($result->getCode(), $code);
    }

    public function checkingkUserAndPasswordProvider(): array
    {
        return [
            ['username', 'username', '', 'it is based on your username', 1],
            ['username', '', 'username', 'it is based upon your password entry', 2],
            ['hxuzohtmn', '', 'hxu zoh tmn', 'it is derived from your password entry', 3],
            ['EStepanis', '', 'Evgeny Stepanischev', 'it is derivable from your password entry', 4],
            ['SEvgen', '', 'Evgeny Stepanischev', "it's derivable from your password entry", 5],
        ];
    }
}
