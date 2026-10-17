<?php

namespace App\Support\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait ResolvesFinancialLookups
{
    /**
     * Resolve daftar generasi dari berbagai kemungkinan sumber.
     * Selalu mengembalikan Collection dengan properti: id, name, code.
     */
    protected function resolveGenerations(): Collection
    {
        foreach ($this->generationCandidates() as $class) {
            if (!class_exists($class)) continue;

            try {
                $model = new $class;
                $table = $model->getTable();
                $cols  = Schema::getColumnListing($table);

                $query = $class::query();

                // Filter aktif bila kolom ada
                if (in_array('is_active', $cols, true)) {
                    $query->where('is_active', true);
                } elseif (in_array('active', $cols, true)) {
                    $query->where('active', true);
                }

                // Urutkan berdasarkan nama bila ada
                $orderCol = collect(['name', 'nama', 'code', 'kode', 'year', 'tahun', 'id'])
                    ->first(fn ($c) => in_array($c, $cols, true));
                if ($orderCol) $query->orderBy($orderCol);

                // Susun select dinamis
                $select = ['id'];
                foreach (['name','nama','code','kode','year','tahun'] as $c) {
                    if (in_array($c, $cols, true)) $select[] = $c;
                }

                $rows = $query->get($select);

                if ($rows->isNotEmpty()) {
                    Log::info('[ResolveGenerations] resolved via '.class_basename($class), ['count' => $rows->count()]);

                    return $rows->map(fn ($g) => (object) [
                        'id'   => $g->id,
                        'name' => $g->name ?? $g->nama ?? ('Generasi #'.$g->id),
                        'code' => $g->code ?? $g->kode ?? '',
                    ])->values();
                }

                Log::warning('[ResolveGenerations] class exists but table empty', [
                    'class' => $class, 'table' => $table,
                ]);
            } catch (\Throwable $e) {
                Log::warning('[ResolveGenerations] candidate failed', [
                    'class' => $class, 'error' => $e->getMessage(),
                ]);
            }
        }

        // ─── Fallback: derive dari users.generation_id / tahun ───
        return $this->deriveGenerationsFromUsers();
    }

    /**
     * Derive generasi dari kolom yang ada di tabel users.
     */
    protected function deriveGenerationsFromUsers(): Collection
    {
        try {
            if (!Schema::hasTable('users')) return collect();

            $cols = Schema::getColumnListing('users');

            $col = collect(['generation_id', 'generation', 'generasi_id', 'generasi', 'angkatan', 'year'])
                ->first(fn ($c) => in_array($c, $cols, true));

            if (!$col) return collect();

            $values = DB::table('users')
                ->whereNotNull($col)
                ->where($col, '!=', '')
                ->distinct()
                ->orderBy($col)
                ->pluck($col);

            if ($values->isEmpty()) return collect();

            Log::info('[ResolveGenerations] derived from users.'.$col, ['count' => $values->count()]);

            return $values->map(fn ($v) => (object) [
                'id'   => $v,
                'name' => 'Generasi '.$v,
                'code' => '',
            ])->values();
        } catch (\Throwable $e) {
            Log::warning('[ResolveGenerations] derive failed', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Resolve daftar anggota (level 3, 4, 5).
     */
    protected function resolveMembers(): Collection
    {
        try {
            $query = \App\Models\User::query()
                ->whereIn('level_id', [3, 4, 5])
                ->orderBy('name');

            $cols = Schema::getColumnListing('users');
            if (in_array('is_active', $cols, true)) {
                $query->where('is_active', true);
            }

            return $query->get(['id', 'name', 'nim']);
        } catch (\Throwable $e) {
            Log::warning('[ResolveMembers] gagal', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Kandidat namespace model generasi — urut dari paling mungkin.
     */
    protected function generationCandidates(): array
    {
        return [
            \App\Models\Financial\Generation::class,
            \App\Models\Generation::class,
            \App\Models\Financial\Generasi::class,
            \App\Models\Generasi::class,
            \App\Models\Financial\GenerationModel::class,
        ];
    }
}