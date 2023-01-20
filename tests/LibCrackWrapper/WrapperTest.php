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

    public function checkingPasswordProvider(): array
    {
        return [
            ['test', 'it is too short'],
            ['abc',  'it is WAY too short'],
            ['xxxxxxx', 'it does not contain enough DIFFERENT characters'],
            ['KPACOTA', 'it is based on a dictionary word'],
            ['ax134178y', 'it looks like a National Insurance number.'],
            ["\t \x0A\x0B\x0C\x0D", 'it is all whitespace'],
            ['01xya12lop23hyr34', 'it is too simplistic/systematic'],
            [strrev('dinosaur'), 'it is based on a (reversed) dictionary word'],
        ];
    }

    /** @covers \LibCrackWrapper\Wrapper::getDefaultDictPath */
    public function testGetDefaultDictPath(): void
    {
        $this->assertNotEmpty(static::$wrapper->getDefaultDictPath());
    }
}
