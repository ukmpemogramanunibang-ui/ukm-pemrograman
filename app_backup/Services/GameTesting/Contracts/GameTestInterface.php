<?php

namespace App\Services\GameTesting\Contracts;

use App\Models\Game;

interface GameTestInterface
{
    public function staticScan(Game $game): array;
    public function runtimeTest(Game $game, array $gameData): array;
    public function playerSimulation(Game $game, array $gameData): array;
    public function getEngineName(): string;
}