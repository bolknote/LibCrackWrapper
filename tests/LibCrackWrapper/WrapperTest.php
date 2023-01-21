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
    public function testCheckPassword(string $pass, string $message): void
    {
        $result = static::$wrapper->checkPassword($pass);

        $this->assertSame($message, (string)$result);
        $this->assertSame($message, $result->getLocalizedMessage());
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
    ): void
    {
        if (static::$wrapper->getBackendName() === 'FFI') {
            $this->assertSame(
                (string) static::$wrapper->checkUserAndPassword($pass, $user, $userinfo),
                $message
            );
        } else {
            $this->markTestSkipped();
        }
    }

    public function checkingkUserAndPasswordProvider(): array
    {
        return [
            ['username', 'username', '', 'it is based on your username'],
            ['username', '', 'username', 'it is based upon your password entry'],
            ['hxuzohtmn', '', 'hxu zoh tmn', 'it is derived from your password entry'],
            ['EStepanis', '', 'Evgeny Stepanischev', 'it is derivable from your password entry'],
            ['SEvgen', '', 'Evgeny Stepanischev', "it's derivable from your password entry"],
        ];
    }

    public function checkingPasswordProvider(): array
    {
        return [
            ['test', 'it is too short'],
            ['abc',  'it is WAY too short'],
            ['xxxxxxx', 'it does not contain enough DIFFERENT characters'],
            ['KPACOTA', 'it is based on a dictionary word'],
            ['ax134178y', 'it looks like a National Insurance number.'],
            ["\t\t \x0B\x0C\x0D\t", 'it is all whitespace'],
            ['01xya12lop23hyr34', 'it is too simplistic/systematic'],
            [strrev('dinosaur'), 'it is based on a (reversed) dictionary word'],
        ];
    }
}
