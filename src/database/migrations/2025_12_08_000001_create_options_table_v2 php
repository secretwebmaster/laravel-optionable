<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * unified creation or upgrade for options table
     */
    public function up(): void
    {
        if (!Schema::hasTable('options')) {
            $this->createNewTable();
        } else {
            $this->upgradeOldTable();
        }
    }

    /**
     * rollback v2 structure (keeps table)
     */
    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            $this->dropIndexIfExists('options', 'options_unique_v2');
            $this->dropIndexIfExists('options', 'options_scope_index');
            $this->dropIndexIfExists('options', 'options_group_index');

            if (Schema::hasColumn('options', 'scope')) {
                $table->dropColumn('scope');
            }
            if (Schema::hasColumn('options', 'group')) {
                $table->dropColumn('group');
            }
            if (Schema::hasColumn('options', 'sort')) {
                $table->dropColumn('sort');
            }

            $table->unique(
                ['optionable_type', 'optionable_id', 'key'],
                'options_optionable_type_optionable_id_key_unique'
            );
        });
    }

    /**
     * create brand new v2 table
     */
    private function createNewTable(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->nullable();
            $table->text('value')->nullable();
            $table->morphs('optionable');
            $table->string('scope', 191)->nullable();
            $table->string('group', 191)->nullable();
            $table->unsignedInteger('sort')->nullable();
            $table->timestamps();

            $table->index('key');
            $table->index('scope');
            $table->index('group');
        });

        $prefix = DB::getTablePrefix();

        try {
            DB::statement("
                ALTER TABLE `{$prefix}options`
                ADD UNIQUE `options_unique_v2`
                (
                    `optionable_type`(100),
                    `optionable_id`,
                    `scope`(100),
                    `group`(100),
                    `key`(150),
                    `sort`
                )
            ");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * upgrade legacy or partially migrated table into v2 format
     */
    private function upgradeOldTable(): void
    {
        $this->dropLegacyIndexes();

        Schema::table('options', function (Blueprint $table) {
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

            $table->index('scope');
            $table->index('group');
        });

        $prefix = DB::getTablePrefix();

        try {
            DB::statement("
                ALTER TABLE `{$prefix}options`
                ADD UNIQUE `options_unique_v2`
                (
                    `optionable_type`(100),
                    `optionable_id`,
                    `scope`(100),
                    `group`(100),
                    `key`(150),
                    `sort`
                )
            ");
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * drop any legacy index regardless of its name
     */
    private function dropLegacyIndexes(): void
    {
        $prefix = DB::getTablePrefix();
        $table = $prefix . 'options';
        $indexes = DB::select("SHOW INDEX FROM `$table`");

        foreach ($indexes as $idx) {
            // unique indexes using old key structure
            $isLegacyUnique = (
                str_contains($idx->Column_name, 'optionable_type') ||
                str_contains($idx->Column_name, 'optionable_id') ||
                str_contains($idx->Column_name, 'key')
            ) && $idx->Non_unique == 0;

            // old scope/group indexes
            $isLegacyScopeGroup = (
                $idx->Key_name === "{$prefix}options_scope_index" ||
                $idx->Key_name === "{$prefix}options_group_index"
            );

            if ($isLegacyUnique || $isLegacyScopeGroup) {
                try {
                    DB::statement("ALTER TABLE `$table` DROP INDEX `{$idx->Key_name}`");
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }
    }

    /**
     * drop index by suffix matching (used in down())
     */
    private function dropIndexIfExists(string $tableName, string $indexSuffix): void
    {
        $prefix = DB::getTablePrefix();
        $fullTable = $prefix . $tableName;

        $indexes = DB::select("SHOW INDEX FROM `$fullTable`");

        foreach ($indexes as $idx) {
            if (str_contains($idx->Key_name, $indexSuffix)) {
                DB::statement("ALTER TABLE `$fullTable` DROP INDEX `{$idx->Key_name}`");
            }
        }
    }
};
