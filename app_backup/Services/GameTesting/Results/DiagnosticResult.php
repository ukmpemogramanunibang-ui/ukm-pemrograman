<?php

namespace App\Services\GameTesting\Results;

class DiagnosticResult
{
    public string $overall = 'pending'; // pending, passed, warning, failed, critical
    public array $summary = ['passed' => 0, 'warning' => 0, 'failed' => 0, 'critical' => 0];
    public array $checks = [];
    public array $runtime = [];
    public array $simulation = [];
    public array $gameDetails = [];
    public ?string $token = null;
    public ?string $sessionId = null;

    public function addCheck(string $key, string $name, string $status, ?string $message = null, array $details = []): self
    {
        $this->checks[] = compact('key', 'name', 'status', 'message', 'details');
        $this->summary[$status] = ($this->summary[$status] ?? 0) + 1;
        return $this;
    }

    public function addRuntime(string $step, string $status, ?string $message = null): self
    {
        $this->runtime[] = compact('step', 'status', 'message');
        return $this;
    }

    public function addSimulation(string $step, string $status, ?string $message = null): self
    {
        $this->simulation[] = compact('step', 'status', 'message');
        return $this;
    }

    public function setOverall(): self
    {
        if ($this->summary['critical'] > 0) {
            $this->overall = 'critical';
        } elseif ($this->summary['failed'] > 0) {
            $this->overall = 'failed';
        } elseif ($this->summary['warning'] > 0) {
            $this->overall = 'warning';
        } elseif ($this->summary['passed'] > 0) {
            $this->overall = 'passed';
        } else {
            $this->overall = 'pending';
        }
        return $this;
    }

    public function isReady(): bool
    {
        return $this->overall === 'passed' || $this->overall === 'warning';
    }

    public function toArray(): array
    {
        return [
            'overall' => $this->overall,
            'summary' => $this->summary,
            'checks' => $this->checks,
            'runtime' => $this->runtime,
            'simulation' => $this->simulation,
            'gameDetails' => $this->gameDetails,
            'token' => $this->token,
            'sessionId' => $this->sessionId,
            'isReady' => $this->isReady(),
        ];
    }
}