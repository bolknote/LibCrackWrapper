<?php
declare(strict_types=1);
namespace LibCrackWrapper;

use PHPUnit\Framework\TestCase;

class WrapperTest extends TestCase
{
    static private Wrapper $wrapper;

    static public function setUpBeforeClass(): void
    {
        static::$wrapper = new Wrapper();
    }

    /**
     * @covers \LibCrackWrapper\Wrapper::checkPassword
     * @dataProvider checkingPasswordProvider
     */
    public function testCheckPassword(string $pass, string $message, int $code): void
    {
        $result = static::$wrapper->checkPassword($pass);

        $this->assertSame($message, (string)$result);
        $this->assertSame($message, $result->getLocalizedMessage());
        $this->assertSame($code, $result->getCode());
    }

    /**
     * @covers \LibCrackWrapper\Wrapper::checkPassword
    */
    public function testCheckPasswordLocalized(): void
    {
        $result = static::$wrapper->checkPassword('123');
        $this->assertNotSame((string) $result, $result->getLocalizedMessage('fr_FR'));
    }

    /**
     * @covers \LibCrackWrapper\Classes\Result::isStrongPassword
     */
    public function testCheckPasswordOK(): void
    {
        $this->assertTrue(
            static::$wrapper->checkPassword('jxtym[jhjibqgfhjkm')->isStrongPassword()
        );

        $this->assertFalse(
            static::$wrapper->checkPassword('123')->isStrongPassword()
        );
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
        if (static::$wrapper->getBackendName() !== 'FFI') {
            $this->markTestSkipped();
        }

        $result = static::$wrapper->checkUserAndPassword($pass, $user, $userinfo);

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

    public function checkingPasswordProvider(): array
    {
        return [
            ['abc',  'it is WAY too short', 7],
            ['test', 'it is too short', 8],
            ['xxxxxxx', 'it does not contain enough DIFFERENT characters', 9],
            ["\t\t \x0B\x0C\x0D\t", 'it is all whitespace', 10],
            ['01xya12lop23hyr34', 'it is too simplistic/systematic', 11],
            ['ax134178y', 'it looks like a National Insurance number.', 12],
            ['KPACOTA', 'it is based on a dictionary word', 13],
            [strrev('dinosaur'), 'it is based on a (reversed) dictionary word', 14],
        ];
    }
}
