<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('materials')) {
            return;
        }

        $hasPanjang = Schema::hasColumn('materials', 'panjang');
        $hasLebar = Schema::hasColumn('materials', 'lebar');
        $hasTinggi = Schema::hasColumn('materials', 'tinggi');

        if (! $hasPanjang && ! $hasLebar && ! $hasTinggi) {
            return;
        }

        if (Schema::hasColumn('materials', 'ukuran')) {
            $query = DB::table('materials')->select('id_material', 'ukuran');

            if ($hasPanjang) {
                $query->addSelect('panjang');
            }

            if ($hasLebar) {
                $query->addSelect('lebar');
            }

            if ($hasTinggi) {
                $query->addSelect('tinggi');
            }

            $query->chunkById(500, function ($rows) use ($hasPanjang, $hasLebar, $hasTinggi): void {
                foreach ($rows as $row) {
                    $existingUkuran = trim((string) ($row->ukuran ?? ''));
                    if ($existingUkuran !== '') {
                        continue;
                    }

                    $parts = [];

                    if ($hasPanjang && $row->panjang !== null) {
                        $parts[] = $this->formatDimensionValue($row->panjang);
                    }

                    if ($hasLebar && $row->lebar !== null) {
                        $parts[] = $this->formatDimensionValue($row->lebar);
                    }

                    if ($hasTinggi && $row->tinggi !== null) {
                        $parts[] = $this->formatDimensionValue($row->tinggi);
                    }

                    if ($parts === []) {
                        continue;
                    }

                    DB::table('materials')
                        ->where('id_material', $row->id_material)
                        ->update(['ukuran' => implode(' x ', $parts)]);
                }
            }, 'id_material');
        }

        $dropColumns = [];
        if ($hasPanjang) {
            $dropColumns[] = 'panjang';
        }
        if ($hasLebar) {
            $dropColumns[] = 'lebar';
        }
        if ($hasTinggi) {
            $dropColumns[] = 'tinggi';
        }

        if ($dropColumns !== []) {
            Schema::table('materials', function (Blueprint $table) use ($dropColumns): void {
                $table->dropColumn($dropColumns);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('materials')) {
            return;
        }

        Schema::table('materials', function (Blueprint $table): void {
            if (! Schema::hasColumn('materials', 'panjang')) {
                $table->decimal('panjang', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('materials', 'lebar')) {
                $table->decimal('lebar', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('materials', 'tinggi')) {
                $table->decimal('tinggi', 12, 2)->nullable();
            }
        });
    }

    private function formatDimensionValue($value): string
    {
        if (! is_numeric($value)) {
            return (string) $value;
        }

        $formatted = number_format((float) $value, 4, '.', '');

        return rtrim(rtrim($formatted, '0'), '.');
    }
};
