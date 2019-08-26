<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinaryRolesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('binary_roles', function (Blueprint $table) {
            $table->binary('role_id');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('binary_roles');
    }
}
