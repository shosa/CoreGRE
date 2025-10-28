<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class CreateCronLogsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('cron_logs', function ($table) {
            $table->id();
            $table->string('job_class', 255)->index();
            $table->string('job_name', 255);
            $table->enum('status', ['running', 'success', 'failed', 'skipped'])->default('running')->index();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('duration_seconds', 10, 3)->nullable();
            $table->string('schedule', 100)->nullable();
            $table->text('output')->nullable();
            $table->timestamps();

            // Indici compositi per query comuni
            $table->index(['job_class', 'status']);
            $table->index(['status', 'started_at']);
        });

        echo "✓ Table 'cron_logs' created successfully\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('cron_logs');
        echo "✓ Table 'cron_logs' dropped successfully\n";
    }
};
