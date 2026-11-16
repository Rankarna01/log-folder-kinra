<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->json('letterhead')->nullable();
            $table->timestamps();
        });

        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->string('division');
            $table->string('position')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('work_days')->nullable(); // [1,2,3,4,5]
            $table->string('status', 30)->default('active'); // active, finished, cancelled
            $table->timestamps();

            $table->index('participant_id');
            $table->index('mentor_id');
        });

        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->cascadeOnDelete();
            $table->date('date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(0);
            $table->string('title', 200);
            $table->string('category', 50)->default('tugas');
            $table->text('raw_description');
            $table->text('ai_description')->nullable();
            $table->text('final_description');
            $table->text('output')->nullable();
            $table->text('obstacles')->nullable();
            $table->string('work_location', 20)->nullable()->default('wfo'); // wfo, wfh, field
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, submitted, approved, revision
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 30)->default('manual'); // manual, voice, offline_sync
            $table->uuid('client_uuid')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['internship_id', 'date']);
            $table->index(['internship_id', 'status']);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained('logbooks')->cascadeOnDelete();
            $table->string('path');
            $table->string('thumb_path')->nullable();
            $table->string('mime', 100)->default('image/jpeg');
            $table->integer('size_kb')->default(0);
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained('logbooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->string('type', 30)->default('comment'); // comment, revision_request, approval
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->cascadeOnDelete();
            $table->string('type', 30)->default('monthly'); // daily, weekly, monthly, custom, final
            $table->date('period_start');
            $table->date('period_end');
            $table->json('options')->nullable();
            $table->longText('ai_summary')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('status', 30)->default('ready'); // queued, processing, ready, failed
            $table->string('verify_token', 64)->unique();
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('feature', 40); // polish, transcribe, photo, summary, skills, final_report
            $table->string('model', 80)->default('gemini-flash-latest');
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->string('status', 30)->default('success');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('activity_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 50)->default('tugas');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->date('date');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_templates');
        Schema::dropIfExists('ai_usages');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('logbooks');
        Schema::dropIfExists('internships');
        Schema::dropIfExists('institutions');
    }
};
