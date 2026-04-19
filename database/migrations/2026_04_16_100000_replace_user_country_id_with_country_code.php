<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop users.country_id whether or not a FK exists, and regardless of MySQL's constraint name.
     */
    protected function dropUsersCountryIdIfPresent(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'country_id')) {
            return;
        }

        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $database = $connection->getDatabaseName();
            $constraints = $connection->select(
                <<<'SQL'
                SELECT DISTINCT kcu.CONSTRAINT_NAME AS CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS tc
                INNER JOIN information_schema.KEY_COLUMN_USAGE kcu
                    ON tc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                    AND tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                    AND tc.TABLE_NAME = kcu.TABLE_NAME
                WHERE tc.TABLE_SCHEMA = ?
                  AND tc.TABLE_NAME = 'users'
                  AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
                  AND kcu.COLUMN_NAME = 'country_id'
                SQL,
                [$database]
            );

            foreach ($constraints as $row) {
                $name = str_replace('`', '``', (string) $row->CONSTRAINT_NAME);
                $connection->statement('ALTER TABLE `users` DROP FOREIGN KEY `'.$name.'`');
            }
        } else {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['country_id']);
                });
            } catch (\Throwable) {
                // Column may exist without a foreign key (e.g. legacy / manual schema).
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'country_id')) {
                $table->dropColumn('country_id');
            }
        });
    }

    public function up(): void
    {
        $this->dropUsersCountryIdIfPresent();

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code', 2)->nullable()->after('institution_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'country_code')) {
                $table->dropColumn('country_code');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('institution_id')->constrained('countries')->nullOnDelete()->cascadeOnUpdate();
            }
        });
    }
};
