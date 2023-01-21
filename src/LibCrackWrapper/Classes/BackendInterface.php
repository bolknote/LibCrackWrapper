<?php
declare(strict_types=1);

namespace LibCrackWrapper\Classes;

use RuntimeException;

abstract class BackendInterface
{
    abstract public function checkPassword(string $password, string $dictpath = null): Result;

    abstract public function checkUserAndPassword(
        string $password,
        string $user,
        string $userinfo,
        string $dictpath = null
    ): Result;

    abstract public function getBackendName(): string;

    protected function setTempLocale(callable $fn, ?string $locale, mixed ...$args): ?string
    {
        if ($locale === null || $locale === 'C') {
            return $fn(...$args);
        } else {

            $old = setlocale(LC_ALL, 0);
            if (setlocale(LC_ALL, $locale) === false) {
                throw new RuntimeException("Invalid locale: {$locale}");
            }

            try {
                return $fn(...$args);
            } finally {
                setlocale(LC_ALL, $old);
            }
        }
    }
}
