<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');  // Assuming you want school_id as a foreign key
            $table->foreignId('student_id')->constrained('student_records')->onDelete('cascade'); // Changed to foreignId
            $table->foreignId('my_classes')->constrained('my_classes')->onDelete('cascade'); // Changed to foreignId
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade'); // Changed to foreignId
            $table->string('attendee'); // Add this line, no changes here unless it's related to another model
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
