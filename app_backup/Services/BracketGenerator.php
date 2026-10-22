<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\CompetitionMatch;
use App\Models\CompetitionParticipant;
use Illuminate\Support\Str;

class BracketGenerator
{
    public function generate(Competition $competition)
    {
        $participants = $competition->participants()->orderBy('seed')->get();
        $count = $participants->count();

        // Tentukan jumlah ronde
        $rounds = ceil(log($count, 2));
        $nearestPower = pow(2, $rounds);
        $byes = $nearestPower - $count;

        // Buat ronde
        for ($i = 1; $i <= $rounds; $i++) {
            $roundName = $this->getRoundName($i, $rounds);
            CompetitionRound::create([
                'competition_id' => $competition->id,
                'name' => $roundName,
                'round_number' => $i,
                'type' => $i == $rounds ? 'final' : 'elimination',
                'status' => $i == 1 ? 'active' : 'upcoming',
            ]);
        }

        // Generate matches untuk ronde 1
        $this->generateRoundMatches($competition, 1, $participants, $byes);
    }

    private function getRoundName($round, $totalRounds)
    {
        $names = [
            1 => 'Babak Awal',
            2 => '16 Besar',
            3 => 'Perempat Final',
            4 => 'Semi Final',
            5 => 'Final',
        ];
        if ($round == $totalRounds) {
            return 'Final';
        }
        return $names[$round] ?? "Ronde {$round}";
    }

    private function generateRoundMatches($competition, $roundNumber, $participants, $byes)
    {
        $round = CompetitionRound::where('competition_id', $competition->id)
            ->where('round_number', $roundNumber)
            ->first();

        $participantCount = count($participants);
        $matchCount = ceil($participantCount / 2);

        $seedPairs = $this->getSeedPairs($participants, $byes);

        $matchNumber = 1;
        foreach ($seedPairs as $pair) {
            $match = CompetitionMatch::create([
                'competition_id' => $competition->id,
                'round_id' => $round->id,
                'match_number' => $matchNumber++,
                'participant_a_id' => $pair['a'] ? $pair['a']->id : null,
                'participant_b_id' => $pair['b'] ? $pair['b']->id : null,
                'status' => ($pair['a'] && $pair['b']) ? 'upcoming' : 'completed',
            ]);

            // Jika hanya satu peserta (bye), langsung loloskan
            if ($pair['a'] && !$pair['b']) {
                $match->update([
                    'winner_id' => $pair['a']->id,
                    'status' => 'completed',
                ]);
            }
        }
    }

    private function getSeedPairs($participants, $byes)
    {
        $pairs = [];
        $len = count($participants);

        // Standard bracket: 1 vs last, 2 vs second last, etc
        $left = 0;
        $right = $len - 1;

        // Handle byes: peserta dengan byes langsung ke ronde berikutnya
        $byeParticipants = [];
        if ($byes > 0) {
            // Ambil peserta dengan seed tertinggi untuk bye
            $byeParticipants = $participants->slice($len - $byes)->values();
            $participants = $participants->slice(0, $len - $byes)->values();
            $len = count($participants);
            $right = $len - 1;
        }

        while ($left <= $right) {
            if ($left == $right) {
                $pairs[] = ['a' => $participants[$left], 'b' => null];
            } else {
                $pairs[] = ['a' => $participants[$left], 'b' => $participants[$right]];
            }
            $left++;
            $right--;
        }

        // Gabungkan dengan bye participants
        foreach ($byeParticipants as $bp) {
            $pairs[] = ['a' => $bp, 'b' => null];
        }

        return $pairs;
    }
}