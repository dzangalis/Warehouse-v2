<?php

namespace records;

use Carbon\Carbon;
use users\User;

class Logger
{
    private array $logs = [];
    private const STORAGE_PATH = "records/";
    public const LOG_TIME_FORMAT = "Y-m-d H:i:s";
    private string $logFile;

    public function __construct()
    {
        $this->logFile = self::STORAGE_PATH . 'logs.log';

        if (!is_dir(self::STORAGE_PATH)) {
            mkdir(self::STORAGE_PATH, 0777, true);
        }

        if (file_exists($this->logFile)) {
            $logs = file_get_contents($this->logFile);
            $this->logs = json_decode($logs, true) ?? [];
        }
    }

    public function log(string $message, ?User $user = null): void
    {
        $timestamp = Carbon::now()->format(self::LOG_TIME_FORMAT);
        $name = $user ? $user->getName() : '';

        $this->logs[] = [
            'timestamp' => $timestamp,
            'name' => $name,
            'message' => $message
        ];

        $this->save();
    }

    private function save(): void
    {
        file_put_contents($this->logFile, json_encode($this->logs, JSON_PRETTY_PRINT));
    }
}