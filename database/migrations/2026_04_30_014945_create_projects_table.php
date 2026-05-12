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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_no')->unique()->comment('เลขโครงการ');
            $table->string('project_name')->comment('ชื่อโครงการ');
            $table->string('fiscal_year', 4)->comment('ปีงบประมาณ');
            $table->string('budget_type')->comment('ประเภทงบ');
            $table->string('responsible_person')->nullable()->comment('ผู้รับผิดชอบโครงการ');
            $table->date('start_date')->nullable()->comment('วันที่เริ่มต้น');
            $table->decimal('total_budget', 15, 2)->default(0)->comment('งบประมาณที่ได้รับจัดสรร');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
