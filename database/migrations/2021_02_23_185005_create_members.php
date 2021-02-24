<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nb_id');
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('formation')->nullable();
            $table->string('county_short')->nullable();
            $table->string('status')->nullable();
            $table->string('org')->nullable();
            $table->string('home_address')->nullable();
            $table->string('active_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->longText('social_media')->nullable();
            $table->string('serie_ci')->nullable();
            $table->string('nr_ci')->nullable();
            $table->string('cnp')->nullable();
            $table->string('gender')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('profession')->nullable();
            $table->string('studies')->nullable();
            $table->longText('political_experience')->nullable();
            $table->string('areas_of_interest')->nullable();
            $table->string('citizenship')->nullable();
            $table->date('started_on')->nullable();
            $table->date('member_fee_paid_until')->nullable();
            $table->string('r_community')->nullable();
            $table->string('r_subsidiary')->nullable();
            $table->string('r_genplus')->nullable();
            $table->string('r_region')->nullable();
            $table->string('last_document_nr')->nullable();
            $table->date('date_last_document_nr')->nullable();
            $table->string('signature')->nullable();
            $table->longText('sanctions')->nullable();
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
        Schema::dropIfExists('members');
    }
}
