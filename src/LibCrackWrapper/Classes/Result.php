<?php
declare(strict_types=1);

namespace LibCrackWrapper\Classes;

final class Result
{
    private $message;
    private $localizeCallback;

    public function __construct(?string $message, callable  $localizeCallback)
    {
        $this->message = $message;
        $this->localizeCallback = $localizeCallback;
    }

    public function __toString()
    {
        return $this->getMessage();
    }

    public function getCode(): int|false
    {
        return array_search($this->message, Constants::STR_RESULTS,true);
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function getLocalizedMessage(string $locale = 'C'): string
    {
        return ($this->localizeCallback)($this->getMessage(), $locale);
    }

    public function isStrongPassword(): bool
    {
        return $this->getCode() === Constants::OK_CODE;
    }
}
