<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('options', function (Blueprint $table) {

            // Remove old unique index if exists
            $this->dropIndexIfExists('options', 'options_optionable_type_optionable_id_key_unique');

            // Add new v2 columns
            if (!Schema::hasColumn('options', 'scope')) {
                $table->string('scope', 191)->after('optionable_id')->nullable();
            }

            if (!Schema::hasColumn('options', 'group')) {
                $table->string('group', 191)->after('scope')->nullable();
            }

            if (!Schema::hasColumn('options', 'sort')) {
                $table->unsignedInteger('sort')->after('group')->nullable();
            }

            try {
                $table->string('key', 191)->nullable()->change();
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->text('value')->nullable()->change();
            } catch (\Throwable $e) {
                // ignore
            }

            // Add safe indexes
            $table->index('scope');
            $table->index('group');
        });

        // Add composite unique index using DB::statement (ensures compatibility)
        try {
            DB::statement("
                ALTER TABLE `options`
                ADD UNIQUE `options_unique_v2`
                (
                    `optionable_type`,
                    `optionable_id`,
                    `scope`,
                    `group`,
                    `key`,
                    `sort`
                )
            ");
        } catch (\Throwable $e) {
            // fallback if already added or running on non-MySQL DB
        }
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {

            // Drop v2 composite index
            $this->dropIndexIfExists('options', 'options_unique_v2');

            // Drop indexes
            $this->dropIndexIfExists('options', 'options_scope_index');
            $this->dropIndexIfExists('options', 'options_group_index');

            // Drop v2 columns
            if (Schema::hasColumn('options', 'scope')) {
                $table->dropColumn('scope');
            }

            if (Schema::hasColumn('options', 'group')) {
                $table->dropColumn('group');
            }

            if (Schema::hasColumn('options', 'sort')) {
                $table->dropColumn('sort');
            }

            // Restore old unique index
            $table->unique(
                ['optionable_type', 'optionable_id', 'key'],
                'options_optionable_type_optionable_id_key_unique'
            );
        });
    }

    private function dropIndexIfExists(string $tableName, string $indexSuffix): void
    {
        $prefix = DB::getTablePrefix();
        $fullTable = $prefix . $tableName;

        // Get indexes from MySQL
        $indexes = DB::select("SHOW INDEX FROM `$fullTable`");

        foreach ($indexes as $idx) {
            // Match by suffix → safe for auto-generated names
            if (str_contains($idx->Key_name, $indexSuffix)) {
                DB::statement("ALTER TABLE `$fullTable` DROP INDEX `{$idx->Key_name}`");
            }
        }
    }
};
