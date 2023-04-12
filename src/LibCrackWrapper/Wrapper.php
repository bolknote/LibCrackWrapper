<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace LibCrackWrapper;
use LibCrackWrapper\Classes\{FFIBackend, CLIBackend, Result};
use RuntimeException;

class Wrapper
{
    public const BACKEND_AUTO = 'auto';
    public const BACKEND_FFI = 'FFI';
    public const BACKEND_CLI = 'CLI';

    private $backend;

    public function __construct(string $backend = self::BACKEND_AUTO)
    {
        switch ($backend) {
            case self::BACKEND_AUTO:
                $this->backend = $this->checkFFI() ? new FFIBackend() : new CLIBackend();
                break;
            case self::BACKEND_CLI:
                $this->backend = new CLIBackend();
                break;
            case self::BACKEND_FFI:
                $this->backend = new FFIBackend();
                break;
            default:
                throw new RuntimeException('Unsupported backend');
        }
    }

    private function checkFFI(): bool
    {
        return extension_loaded('ffi') && ini_get('ffi.enable') === '1';
    }

    public function checkPassword(string $password, string $dictpath = null): Result
    {
        return $this->backend->checkPassword($password, $dictpath);
    }

    public function getBackendName(): string
    {
        return $this->backend->getBackendName();
    }

    /** @noinspection PhpUnused */
    public function checkUserAndPassword(
            string $password,
            string $user,
            string $userinfo,
            string $dictpath = null
    ): Result
    {
        return $this->backend->checkUserAndPassword($password, $user, $userinfo, $dictpath);
    }
}
