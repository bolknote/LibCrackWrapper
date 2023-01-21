<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace LibCrackWrapper;
use LibCrackWrapper\Classes\{BackendInterface, FFIBackend, CLIBackend, Result};

class Wrapper
{
    private BackendInterface $backend;

    public function __construct()
    {
        $this->backend = $this->checkFFI() ? new FFIBackend() : new CLIBackend();
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
