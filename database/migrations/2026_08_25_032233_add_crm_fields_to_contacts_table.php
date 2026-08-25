<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('source')->default('Website');
            $table->string('priority')->default('Medium');
            $table->decimal('deal_value', 10, 2)->default(0.00);
            $table->date('follow_up_date')->nullable();
            $table->text('internal_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['source', 'priority', 'deal_value', 'follow_up_date', 'internal_notes']);
        });
    }
};