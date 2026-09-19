<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Upgrade audit_logs (kalau belum ada → create)
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $t) {
                $t->id();
                $t->uuid('event_uuid')->unique();

                // Actor
                $t->string('actor_type', 32)->default('user');
                $t->unsignedBigInteger('actor_id')->nullable();
                $t->string('actor_name')->nullable();
                $t->string('actor_role')->nullable();
                $t->string('actor_position')->nullable();

                // Target user (user yang terdampak)
                $t->unsignedBigInteger('target_user_id')->nullable();

                // Action
                $t->string('action', 96)->index();
                $t->string('category', 48)->nullable()->index();
                $t->string('severity', 24)->default('info')->index();
                $t->string('result', 32)->default('success')->index();

                // Entity universal
                $t->string('auditable_type')->nullable()->index();
                $t->unsignedBigInteger('auditable_id')->nullable()->index();

                // Context references
                $t->unsignedBigInteger('session_id')->nullable()->index();
                $t->unsignedBigInteger('group_id')->nullable()->index();
                $t->unsignedBigInteger('room_id')->nullable()->index();

                // Data
                $t->text('description')->nullable();
                $t->json('before_data')->nullable();
                $t->json('after_data')->nullable();
                $t->json('change_data')->nullable();
                $t->text('reason')->nullable();

                // Authorization
                $t->string('permission', 128)->nullable();
                $t->string('authorization_result', 24)->nullable();

                // Source & device
                $t->string('source', 32)->default('web')->index();
                $t->string('ip_address', 45)->nullable();
                $t->text('user_agent')->nullable();
                $t->string('device_id', 128)->nullable();

                // Tracing
                $t->string('request_id', 64)->nullable()->index();
                $t->string('correlation_id', 64)->nullable()->index();

                // Impact
                $t->unsignedInteger('affected_count')->default(0);
                $t->json('affected_ids')->nullable();

                // Integrity
                $t->string('previous_hash', 64)->nullable();
                $t->string('integrity_hash', 64)->nullable()->index();

                // Flags
                $t->boolean('is_sensitive')->default(false)->index();
                $t->timestamp('created_at')->useCurrent()->index();

                // Legacy compat
                $t->unsignedBigInteger('user_id')->nullable()->index();

                // Composite indexes
                $t->index(['actor_id', 'created_at']);
                $t->index(['target_user_id', 'created_at']);
                $t->index(['action', 'created_at']);
                $t->index(['severity', 'created_at']);
                $t->index(['result', 'created_at']);
                $t->index(['source', 'created_at']);
            });
        } else {
            Schema::table('audit_logs', function (Blueprint $t) {
                $add = fn($col) => !Schema::hasColumn('audit_logs', $col);
                if ($add('event_uuid'))              $t->uuid('event_uuid')->nullable()->unique()->after('id');
                if ($add('actor_type'))              $t->string('actor_type', 32)->default('user');
                if ($add('actor_id'))                $t->unsignedBigInteger('actor_id')->nullable()->index();
                if ($add('actor_name'))              $t->string('actor_name')->nullable();
                if ($add('actor_role'))              $t->string('actor_role')->nullable();
                if ($add('actor_position'))          $t->string('actor_position')->nullable();
                if ($add('target_user_id'))          $t->unsignedBigInteger('target_user_id')->nullable()->index();
                if ($add('category'))                $t->string('category', 48)->nullable()->index();
                if ($add('severity'))                $t->string('severity', 24)->default('info')->index();
                if ($add('result'))                  $t->string('result', 32)->default('success')->index();
                if ($add('auditable_type'))          $t->string('auditable_type')->nullable()->index();
                if ($add('auditable_id'))            $t->unsignedBigInteger('auditable_id')->nullable()->index();
                if ($add('session_id'))              $t->unsignedBigInteger('session_id')->nullable()->index();
                if ($add('group_id'))                $t->unsignedBigInteger('group_id')->nullable()->index();
                if ($add('room_id'))                 $t->unsignedBigInteger('room_id')->nullable()->index();
                if ($add('before_data'))             $t->json('before_data')->nullable();
                if ($add('after_data'))              $t->json('after_data')->nullable();
                if ($add('change_data'))             $t->json('change_data')->nullable();
                if ($add('reason'))                  $t->text('reason')->nullable();
                if ($add('permission'))              $t->string('permission', 128)->nullable();
                if ($add('authorization_result'))    $t->string('authorization_result', 24)->nullable();
                if ($add('source'))                  $t->string('source', 32)->default('web')->index();
                if ($add('device_id'))               $t->string('device_id', 128)->nullable();
                if ($add('request_id'))              $t->string('request_id', 64)->nullable()->index();
                if ($add('correlation_id'))          $t->string('correlation_id', 64)->nullable()->index();
                if ($add('affected_count'))          $t->unsignedInteger('affected_count')->default(0);
                if ($add('affected_ids'))            $t->json('affected_ids')->nullable();
                if ($add('previous_hash'))           $t->string('previous_hash', 64)->nullable();
                if ($add('integrity_hash'))          $t->string('integrity_hash', 64)->nullable()->index();
                if ($add('is_sensitive'))            $t->boolean('is_sensitive')->default(false)->index();
            });
        }

        // 2) Security alerts
        if (!Schema::hasTable('audit_alerts')) {
            Schema::create('audit_alerts', function (Blueprint $t) {
                $t->id();
                $t->uuid('alert_uuid')->unique();
                $t->string('type', 48)->index();
                $t->string('severity', 24)->index();
                $t->string('title');
                $t->text('message')->nullable();
                $t->json('context')->nullable();
                $t->string('related_event_uuid', 36)->nullable();
                $t->timestamp('acknowledged_at')->nullable();
                $t->unsignedBigInteger('acknowledged_by')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_alerts');
    }
};