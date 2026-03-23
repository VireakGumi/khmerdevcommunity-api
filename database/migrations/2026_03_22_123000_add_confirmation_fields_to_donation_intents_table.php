<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_intents', function (Blueprint $table) {
            $table->string('transfer_reference')->nullable()->after('status');
            $table->string('proof_image_url')->nullable()->after('note');
            $table->timestamp('confirmed_at')->nullable()->after('proof_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('donation_intents', function (Blueprint $table) {
            $table->dropColumn(['transfer_reference', 'proof_image_url', 'confirmed_at']);
        });
    }
};
