<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS users_before_delete_anchor_users');
        DB::unprepared('
            CREATE TRIGGER users_before_delete_anchor_users
            BEFORE DELETE ON users
            FOR EACH ROW
            BEGIN
                IF IFNULL(OLD.developer_anchor, 0) = 1 OR IFNULL(OLD.super_admin_anchor, 0) = 1 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Cannot delete anchor system user\';
                END IF;
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS users_before_update_anchor_soft_delete');
        DB::unprepared('
            CREATE TRIGGER users_before_update_anchor_soft_delete
            BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
                    IF IFNULL(NEW.developer_anchor, 0) = 1 OR IFNULL(NEW.super_admin_anchor, 0) = 1 THEN
                        SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Cannot delete anchor system user\';
                    END IF;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS users_before_delete_anchor_users');
        DB::unprepared('DROP TRIGGER IF EXISTS users_before_update_anchor_soft_delete');
    }
};
