<?php

declare(strict_types=1);

namespace Modules\Client\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Class PasswordRestoreService
 * @package Modules\Client\Services
 */
class PasswordRestoreService
{
    /** @var int Maximum number of code entry attempts. */
    private int $maxCodeEntryAttempts;

    /** @var string Prefix of session variables. */
    private string $sessionVarPrefix;

    /** @var string Prefix of routes names. */
    private string $routesNamesPrefix;

    /** @var string[] Names of session variables. */
    private const SESSION_VAR_NAMES = [
            'code'      => 'code',
            'client_id' => 'client_id',
            'verified'  => 'is_code_verified',
            'attempts'  => 'count_attempts',
        ];

    public function __construct()
    {
        $moduleName = Str::singular(request()->segment(1) ?? 'client');

        $this->routesNamesPrefix    = Config::get($moduleName . '.routes_names_prefix');
        $this->sessionVarPrefix     = Config::get($moduleName . '.session_var_prefix');
        $this->maxCodeEntryAttempts = Config::get($moduleName . '.max_code_entry_attempts');
    }

    /**
     * Generates a recovery code.
     */
    public function generateCode(): self
    {
        $code = mt_rand(100000, 999999);

        $this->rememberCode($code);

        return $this;
    }

    /**
     * Returns the recovery code.
     */
    public function getCode(): ?int
    {
        return Session::get($this->sessionVarPrefix . self::SESSION_VAR_NAMES['code']) ?? null;
    }

    /**
     * Set attempts count
     */
    public function setAttemptsCount(int $qty): self
    {
        Session::put($this->sessionVarPrefix . self::SESSION_VAR_NAMES['attempts'], $qty);

        return $this;
    }

    /**
     * Saves Client ID to session.
     */
    public function rememberClient(int $clientId): self
    {
        Session::put($this->sessionVarPrefix . self::SESSION_VAR_NAMES['client_id'], $clientId);

        return $this;
    }

    /**
     * Checking attempts to entry a recovery code
     */
    public function checkCodeEntryAttempts(): bool
    {
        $attempts = Session::get($this->sessionVarPrefix . self::SESSION_VAR_NAMES['attempts']) ?? 0;
        Session::put($this->sessionVarPrefix . self::SESSION_VAR_NAMES['attempts'], ++$attempts);

        return ($this->maxCodeEntryAttempts + 1 > $attempts);
    }

    /**
     * Returns the Client ID
     */
    public function getClientId(): ?int
    {
        return Session::get($this->sessionVarPrefix . self::SESSION_VAR_NAMES['client_id']) ?? null;
    }

    /**
     * Reset recovery code validation.
     */
    public function resetCodeCheck(): self
    {
        Session::forget($this->sessionVarPrefix . self::SESSION_VAR_NAMES['verified']);

        return $this;
    }

    /**
     * Reset Client ID.
     */
    public function resetClientId(): self
    {
        Session::forget($this->sessionVarPrefix . self::SESSION_VAR_NAMES['client_id']);

        return $this;
    }

    /**
     * Remembers that the recovery code has been successfully validated.
     */
    public function codeSuccessfullyVerified(): void
    {
        $this->resetCode();
        $this->resetAttempts();

        Session::put($this->sessionVarPrefix . self::SESSION_VAR_NAMES['verified'], true);
    }

    /**
     * Returns the status of the recovery code verification
     */
    public function isCodeVerified(): ?bool
    {
        return Session::get($this->sessionVarPrefix . self::SESSION_VAR_NAMES['verified']) ?? null;
    }

    /**
     * Returns the URL to which the user will be redirected after the first step of password recovery.
     */
    public function redirectToSecondStep(): string
    {
        return URL::route($this->routesNamesPrefix . 'second_step', [], false);
    }

    /**
     * Returns the URL to which the user will be redirected after the second step of password recovery.
     */
    public function redirectToThirdStep(): string
    {
        return URL::route($this->routesNamesPrefix . 'third_step', [], false);
    }

    /**
     * Saves code to session.
     */
    private function rememberCode(int $code): void
    {
        Session::put($this->sessionVarPrefix . self::SESSION_VAR_NAMES['code'], $code);
    }

    /**
     * Reset recovery code.
     */
    private function resetCode(): void
    {
        Session::forget($this->sessionVarPrefix . self::SESSION_VAR_NAMES['code']);
    }

    /**
     * Reset attempts.
     */
    private function resetAttempts(): void
    {
        Session::forget($this->sessionVarPrefix . self::SESSION_VAR_NAMES['attempts']);
    }
}
