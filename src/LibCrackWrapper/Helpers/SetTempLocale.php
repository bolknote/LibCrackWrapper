<?php
declare(strict_types=1);

namespace LibCrackWrapper\Helpers;

use RuntimeException;

trait SetTempLocale
{
    protected function setTempLocale(callable $fn, ?string $locale, mixed ...$args): mixed
    {
        if ($locale === null) {
            return $fn(...$args);
        } else {
            $old = setlocale(LC_MESSAGES, 0);
            if (setlocale(LC_MESSAGES, $locale) === false) {
                throw new RuntimeException("Invalid locale: {$locale}");
            }

            try {
                return $fn(...$args);
            } finally {
                setlocale(LC_MESSAGES, $old);
            }
        }
    }
}
