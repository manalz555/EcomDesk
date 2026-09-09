<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Live chat widget support:
 * - clients.widget_token identifies an anonymous website visitor (stored in
 *   their browser's localStorage) so follow-up messages reach the same record.
 * - reponses.is_client + nullable agent_id let a conversation's history hold
 *   messages *from* the client, not only agent replies — needed by any real
 *   inbound channel, the widget being the first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('widget_token', 64)->nullable()->unique()->after('telegram_chat_id');
        });

        Schema::table('reponses', function (Blueprint $table) {
            $table->boolean('is_client')->default(false)->after('is_draft');
        });

        // Client messages have no agent author: drop the NOT NULL constraint.
        // Raw SQL on MySQL because ->change() on a foreign-keyed column is
        // unreliable there; the portable Blueprint form is used elsewhere.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE reponses MODIFY agent_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('reponses', function (Blueprint $table) {
                $table->foreignId('agent_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('widget_token');
        });

        Schema::table('reponses', function (Blueprint $table) {
            $table->dropColumn('is_client');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE reponses MODIFY agent_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('reponses', function (Blueprint $table) {
                $table->foreignId('agent_id')->nullable(false)->change();
            });
        }
    }
};
