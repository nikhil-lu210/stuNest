<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop all foreign keys on `applications` if present (names differ across DBs / legacy schemas).
     */
    protected function dropApplicationsForeignKeys(): void
    {
        if (! Schema::hasTable('applications')) {
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
                  AND tc.TABLE_NAME = 'applications'
                  AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
                  AND kcu.COLUMN_NAME IN ('property_id', 'user_id')
                SQL,
                [$database]
            );

            foreach ($constraints as $row) {
                $name = str_replace('`', '``', (string) $row->CONSTRAINT_NAME);
                $connection->statement('ALTER TABLE `applications` DROP FOREIGN KEY `'.$name.'`');
            }

            return;
        }

        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
                $table->dropForeign(['user_id']);
            });
        } catch (\Throwable) {
            // No foreign keys or non-standard schema (e.g. SQLite tests).
        }
    }

    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'available_from')) {
                $table->date('available_from')->nullable()->after('available_beds');
            }
            if (! Schema::hasColumn('properties', 'min_contract_weeks')) {
                $table->unsignedSmallInteger('min_contract_weeks')->nullable()->after('available_from');
            }
        });

        if (Schema::hasTable('properties')) {
            DB::table('properties')->orderBy('id')->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    $weeks = match ($row->min_contract_length ?? 'flexible') {
                        '1_month' => 4,
                        '3_months' => 13,
                        '6_months' => 26,
                        '1_year' => 52,
                        'flexible' => 1,
                        default => 26,
                    };
                    DB::table('properties')->where('id', $row->id)->update([
                        'min_contract_weeks' => $weeks,
                    ]);
                }
            });
        }

        if (Schema::hasTable('applications')) {
            // InnoDB may use the composite unique index for FK lookups; drop FKs before dropping unique.
            $this->dropApplicationsForeignKeys();

            Schema::table('applications', function (Blueprint $table) {
                $table->dropUnique(['property_id', 'user_id']);
            });

            Schema::table('applications', function (Blueprint $table) {
                if (! Schema::hasColumn('applications', 'proposed_move_in')) {
                    $table->date('proposed_move_in')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('applications', 'proposed_duration_weeks')) {
                    $table->unsignedSmallInteger('proposed_duration_weeks')->nullable()->after('proposed_move_in');
                }
                if (! Schema::hasColumn('applications', 'message_to_landlord')) {
                    $table->text('message_to_landlord')->nullable()->after('proposed_duration_weeks');
                }
            });

            Schema::table('applications', function (Blueprint $table) {
                $table->index(['property_id', 'user_id', 'status']);
            });

            Schema::table('applications', function (Blueprint $table) {
                $table->foreign('property_id')
                    ->references('id')
                    ->on('properties')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            $this->dropApplicationsForeignKeys();

            Schema::table('applications', function (Blueprint $table) {
                $table->dropIndex(['property_id', 'user_id', 'status']);
            });

            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'message_to_landlord')) {
                    $table->dropColumn('message_to_landlord');
                }
                if (Schema::hasColumn('applications', 'proposed_duration_weeks')) {
                    $table->dropColumn('proposed_duration_weeks');
                }
                if (Schema::hasColumn('applications', 'proposed_move_in')) {
                    $table->dropColumn('proposed_move_in');
                }
            });

            Schema::table('applications', function (Blueprint $table) {
                $table->unique(['property_id', 'user_id']);
            });

            Schema::table('applications', function (Blueprint $table) {
                $table->foreign('property_id')
                    ->references('id')
                    ->on('properties')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            });
        }

        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'min_contract_weeks')) {
                $table->dropColumn('min_contract_weeks');
            }
            if (Schema::hasColumn('properties', 'available_from')) {
                $table->dropColumn('available_from');
            }
        });
    }
};
