<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('selllers', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->jsonb('phones')->nullable();
            $table->string('email')->nullable()->unique();


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('selllers');
    }
};
