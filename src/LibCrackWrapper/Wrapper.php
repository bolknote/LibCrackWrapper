<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace LibCrackWrapper;

use FFI;
use FFI\Exception;
use LibCrackWrapper\Helpers\SetTempLocale;
use RuntimeException;

class Wrapper
{
    use SetTempLocale;
    private FFI $ffi;

    public function __construct()
    {
        $cdefs = file_get_contents(
                __FILE__,
                false,
                null,
                __COMPILER_HALT_OFFSET__
        );

        try {
            $lib = PHP_OS === 'Darwin' ? 'libcrack.2.dylib' : 'libcrack.so.2';
            $this->ffi = FFI::cdef($cdefs, $lib);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

        if (PHP_OS === 'Darwin') {
            $current = $this->ffi->bindtextdomain('cracklib', null);

            if (stripos('/cracklib/', $current) === false) {
                // Dirty hack for cracklib installed via brew
                if ($paths = glob('/usr/local/Cellar/cracklib/*/share/locale/')) {
                    $this->ffi->bindtextdomain('cracklib', $paths[0]);
                }
            }
        }
    }

    private function result(?string $result): object
    {
        return new class($this->ffi, $result) {
            use SetTempLocale;

            private const RESULTS = [
                null,
                'it is based on your username',
                'it is based upon your password entry',
                'it is derived from your password entry',
                'it is derivable from your password entry',
                "it's derivable from your password entry",
                'you are not registered in the password file',
                'it is WAY too short',
                'it is too short',
                'it does not contain enough DIFFERENT characters',
                'it is all whitespace',
                'it is too simplistic/systematic',
                'it looks like a National Insurance number.',
                'it is based on a dictionary word',
                'it is based on a (reversed) dictionary word',
                'error loading dictionary',
                "it's derived from your password entry",
            ];

            public function __construct(private readonly FFI $ffi, private readonly ?string $result) {}

            public function __toString(): string
            {
                return $this->result ?? '';
            }

            /** @noinspection PhpUnused */
            public function getLocalizedMessage(string $locale = 'C'): string
            {
                if ($this->result === null || $locale === 'C') {
                    return $this->result ?? '';
                } else {
                    return $this->setTempLocale(
                            function ($message) {
                                return $this->ffi->dgettext("cracklib", $message);
                            },
                            $locale,
                            $this->result,
                    );
                }
            }

            public function getCode(): int|false
            {
                return array_search($this->result, static::RESULTS,true);
            }
        };
    }

    public function getDefaultDictPath(): string
    {
        return $this->ffi->GetDefaultCracklibDict();
    }

    public function checkPassword(string $password, string $dictpath = null): object
    {
        return $this->result(
            $this->setTempLocale([$this->ffi, 'FascistCheck'],
                'C',
                $password,
                $dictpath ?? $this->getDefaultDictPath()
            )
        );
    }

    /** @noinspection PhpUnused */
    public function checkUserAndPassword(
            string $user,
            string $password,
            string $userinfo,
            string $dictpath = null
    ): object
    {
        return $this->result(
                $this->setTempLocale([$this->ffi, 'FascistCheckUser'],
                    'C',
                    $password,
                    $dictpath ?? $this->getDefaultDictPath(),
                    $user,
                    $userinfo
                )
        );
    }
}

// Cdefs for FFI
__halt_compiler();

const char *GetDefaultCracklibDict(void);
const char *FascistCheck(const char *pw, const char *dictpath);
const char *FascistCheckUser(const char *pw, const char *dictpath, const char *user, const char *gecos);
const char *dgettext(const char * domainname, const char * msgid);
const char *bindtextdomain (const char * domainname, const char * dirname);
