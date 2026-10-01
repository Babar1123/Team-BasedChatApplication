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
        Schema::table('teams', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('name');
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('messages')->nullOnDelete();
            $table->softDeletes();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'admin', 'member'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });

        Schema::create('channel_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['channel_id', 'user_id']);
        });

        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction');
            $table->timestamps();
            $table->unique(['message_id', 'user_id', 'reaction']);
        });

        Schema::table('user_tokens', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('token');
            $table->timestamp('expires_at')->nullable()->after('device_name');
            $table->timestamp('last_used_at')->nullable()->after('expires_at');
            $table->timestamp('revoked_at')->nullable()->after('last_used_at');
            $table->index(['user_id', 'revoked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('channel_members');
        Schema::dropIfExists('team_members');

        Schema::table('user_tokens', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'expires_at', 'last_used_at', 'revoked_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('is_private');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
