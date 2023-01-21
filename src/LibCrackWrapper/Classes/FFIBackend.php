<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace LibCrackWrapper\Classes;

use FFI;
use FFI\Exception;
use RuntimeException;

final class FFIBackend extends BackendInterface
{
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

    public function localizeCallback(string $message, string $locale = 'C'): string
    {
        if ($locale === 'C') {
            return $message;
        } else {
            return $this->setTempLocale(
                fn ($message) => $this->ffi->dgettext("cracklib", $message),
                $locale,
                $message,
            );
        }
    }

    public function checkPassword(string $password, string $dictpath = null): Result
    {
        return new Result(
            $this->setTempLocale([$this->ffi, 'FascistCheck'],
                'C',
                $password,
                $dictpath
            ),
            [$this, 'localizeCallback']
        );
    }

    public function checkUserAndPassword(
        string $password,
        string $user,
        string $userinfo,
        string $dictpath = null
    ): Result
    {
        return new Result(
            $this->setTempLocale([$this->ffi, 'FascistCheckUser'],
                'C',
                $password,
                $dictpath,
                $user,
                $userinfo
            ),
            [$this, 'localizeCallback']
        );
    }

    public function getBackendName(): string
    {
        return 'FFI';
    }
}

// Cdefs for FFI
__halt_compiler();

const char *FascistCheck(const char *pw, const char *dictpath);
const char *FascistCheckUser(const char *pw, const char *dictpath, const char *user, const char *gecos);
const char *dgettext(const char * domainname, const char * msgid);
const char *bindtextdomain (const char * domainname, const char * dirname);
