<?php
declare(strict_types=1);
namespace LibCrackWrapper;

use PHPUnit\Framework\TestCase;

abstract class WrapperTest extends TestCase
{
    static protected $wrapper;
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

    public static function checkingPasswordProvider(): array
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
