<?php

namespace App\Services;

interface ClientServiceInterface
{
    public function handleClientMove(int $clientId, int $targetChannelId): void;
}
