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
            $table->id(); // Creates the primary key as unsignedBigInteger
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedInteger('student_id'); // Use unsignedInteger instead of foreignId to match students.id
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade'); // Correct foreign key definition
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
