<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('customer_mobile_no');
            $table->string('email_id');
            $table->string('customer_name');
            $table->string('property_for');
            $table->string('property_type');
            $table->decimal('price', 15, 2);
            $table->decimal('booking_amount_security_amount', 15, 2)->default(0);
            $table->decimal('maintenance_charges', 15, 2)->default(0);
            $table->text('address');
            $table->integer('state');
            $table->integer('city');
            $table->string('location_colony')->nullable();
            $table->string('name_of_project_society')->nullable();
            $table->decimal('covered_area', 10, 2)->nullable();
            $table->string('ca_unit')->nullable();
            $table->decimal('plot_area', 10, 2)->nullable();
            $table->string('pa_unit')->nullable();
            $table->integer('no_of_bedroom')->nullable();
            $table->integer('no_of_bathroom')->nullable();
            $table->integer('no_of_balconies')->nullable();
            $table->integer('furnished')->default(false);
            $table->string('possession_status')->nullable();
            $table->integer('age_of_const')->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('total_floors_in_building')->nullable();
            $table->boolean('personal_pantry')->default(false);
            $table->boolean('personal_washroom')->default(false);
            $table->integer('floors_allowed_for_construction')->nullable();
            $table->boolean('any_construction_done')->default(false);
            $table->boolean('boundary_wall_made')->default(false);
            $table->boolean('is_in_a_gated_colony')->default(false);
            $table->string('transaction_type')->nullable();
            $table->string('additional_rooms')->nullable();
            $table->integer('no_of_car_parking_covered')->nullable();
            $table->integer('no_of_car_parking_open')->nullable();
            $table->integer('number_seats')->nullable();
            $table->string('type_of_coworking_space')->nullable();
            $table->user('user_id')->default(1);
            $table->json('amenities')->nullable();
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
        Schema::dropIfExists('properties');
    }
}
