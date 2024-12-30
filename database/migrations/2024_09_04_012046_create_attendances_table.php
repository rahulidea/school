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
            $table->id();  // Primary key, automatically unsignedBigInteger
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');  // Ensure foreign key references 'students.id'
            $table->foreignId('my_classes')->constrained('my_classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('attendee');
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
