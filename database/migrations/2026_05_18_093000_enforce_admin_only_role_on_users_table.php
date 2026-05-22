<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('role')
            ->orWhere('role', '!=', 'admin')
            ->update(['role' => 'admin']);

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin') NOT NULL DEFAULT 'admin'");

            return;
        }

        if ($driver === 'pgsql') {
            $constraints = DB::select(
                "
                SELECT c.conname
                FROM pg_constraint c
                JOIN pg_class t ON t.oid = c.conrelid
                JOIN pg_namespace n ON n.oid = t.relnamespace
                WHERE t.relname = 'users'
                  AND n.nspname = current_schema()
                  AND pg_get_constraintdef(c.oid) ILIKE '%role%'
                "
            );

            foreach ($constraints as $constraint) {
                DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS ' . $constraint->conname);
            }

            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(20)");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'admin'");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET NOT NULL");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_admin_only_check CHECK (role = 'admin')");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'supervisor') NOT NULL DEFAULT 'admin'");

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_admin_only_check");
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP NOT NULL");
        }
    }
};
