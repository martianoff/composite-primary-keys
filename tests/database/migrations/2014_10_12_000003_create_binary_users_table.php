<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinaryUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('binary_users', function (Blueprint $table) {
            $table->binary('user_id');
            $table->unsignedInteger('organization_id');
            $table->primary(['user_id', 'organization_id']);
            $table->unsignedInteger('role_id');
            $table->binary('binary_role_id')->nullable();
            $table->string('name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('binary_users');
    }
}
