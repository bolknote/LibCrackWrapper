<?php
declare(strict_types=1);

namespace LibCrackWrapper\Classes;

use RuntimeException;

final class CLIBackend extends BackendInterface
{
    private $librack;

    public function __construct()
    {
        $this->librack = null;

        foreach (explode(PATH_SEPARATOR, (string) getenv('PATH')) as $path)
        {
            $path .= '/cracklib-check';
            if (file_exists($path) && is_executable($path)) {
                $this->librack = escapeshellcmd($path);
                break;
            }
        }

        if ($this->librack === null) {
            throw new RuntimeException('Cannot find cracklib-check utility');
        }

        if (PHP_OS_FAMILY === 'Darwin' && extension_loaded('gettext')) {
            $current = bindtextdomain('cracklib', null);

            if (stripos('/cracklib/', $current) === false) {
                // Dirty hack for cracklib installed via brew
                if ($paths = glob('/usr/local/Cellar/cracklib/*/share/locale/')) {
                    bindtextdomain('cracklib', $paths[0]);
                }
            }
        }
    }

    /** @noinspection PhpComposerExtensionStubsInspection */
    public function localizeCallback(string $message, string $locale = 'C'): string
    {
        if ($locale === 'C' || !extension_loaded('gettext')) {
            return $message;
        } else {
            return $this->setTempLocale(
                static function ($message) {
                    return dgettext("cracklib", $message);
                },
                $locale,
                $message,
            );
        }
    }

    private function prepare(string $str, string $pass): ?string
    {
        // Format: «pass: message»
        $str = trim(substr($str, strlen($pass) + 1));
        return $str === 'OK' ? null : $str;
    }

    public function checkPassword(string $password, string $dictpath = null): Result
    {
        $descriptorspec = [
            ["pipe", "r"],
            ["pipe", "w"],
            ["file", "/dev/null", 'w'],
        ];

        $cmd = $this->librack;

        if ($dictpath !== null) {
            $cmd .= ' ' . escapeshellarg($dictpath);
        }

        $process = proc_open($cmd, $descriptorspec, $pipes);
        fwrite($pipes[0], $password . "\n");
        fclose($pipes[0]);

        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);

        return new Result(
            $this->prepare($result, $password),
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
        throw new RuntimeException('Cannot be implemented');
    }

    public function getBackendName(): string
    {
        return 'CLI';
    }
}
