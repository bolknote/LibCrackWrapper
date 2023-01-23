<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace LibCrackWrapper;
use LibCrackWrapper\Classes\{BackendInterface, FFIBackend, CLIBackend, Result};

class Wrapper
{
    public const BACKEND_AUTO = 'auto';
    public const BACKEND_FFI = 'FFI';
    public const BACKEND_CLI = 'CLI';

    private BackendInterface $backend;

    public function __construct(string $backend = self::BACKEND_AUTO)
    {
        $this->backend = match ($backend) {
            self::BACKEND_AUTO => $this->checkFFI() ? new FFIBackend() : new CLIBackend(),
            self::BACKEND_CLI  => new CLIBackend(),
            self::BACKEND_FFI  => new FFIBackend(),
        };
    }

    private function checkFFI(): bool
    {
        return extension_loaded('ffi');
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
