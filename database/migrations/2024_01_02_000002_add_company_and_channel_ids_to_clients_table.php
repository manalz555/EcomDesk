<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->string('avatar_path')->nullable()->after('notes');

            // Identifiants externes par canal, utilises pour rapprocher automatiquement
            // un message entrant a une fiche client existante une fois les API connectees.
            $table->string('whatsapp_id')->nullable()->after('telephone');
            $table->string('instagram_handle')->nullable()->after('whatsapp_id');
            $table->string('messenger_psid')->nullable()->after('instagram_handle');
            $table->string('telegram_chat_id')->nullable()->after('messenger_psid');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['avatar_path', 'whatsapp_id', 'instagram_handle', 'messenger_psid', 'telegram_chat_id']);
        });
    }
};
