<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        if (! Schema::hasColumn('products', 'ukuran')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->string('ukuran', 100)->nullable();
            });
        }

        $hasPanjang = Schema::hasColumn('products', 'panjang');
        $hasLebar = Schema::hasColumn('products', 'lebar');
        $hasTinggi = Schema::hasColumn('products', 'tinggi');

        if (! $hasPanjang && ! $hasLebar && ! $hasTinggi) {
            return;
        }

        $query = DB::table('products')->select('id_product', 'ukuran', 'satuan');

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

                $ukuran = implode(' x ', $parts);
                $satuan = trim((string) ($row->satuan ?? ''));
                if ($satuan !== '') {
                    $ukuran .= ' ' . $satuan;
                }

                DB::table('products')
                    ->where('id_product', $row->id_product)
                    ->update(['ukuran' => $ukuran]);
            }
        }, 'id_product');

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
            Schema::table('products', function (Blueprint $table) use ($dropColumns): void {
                $table->dropColumn($dropColumns);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'panjang')) {
                $table->decimal('panjang', 10, 2)->nullable();
            }

            if (! Schema::hasColumn('products', 'lebar')) {
                $table->decimal('lebar', 10, 2)->nullable();
            }

            if (! Schema::hasColumn('products', 'tinggi')) {
                $table->decimal('tinggi', 10, 2)->nullable();
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
