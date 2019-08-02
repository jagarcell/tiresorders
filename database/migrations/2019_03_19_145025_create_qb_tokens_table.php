<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQbTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('accesstoken', 1000);
            $table->string('refreshtoken', 1000);
            $table->string('state');
            $table->string('code');
            $table->string('realmid');
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
        Schema::dropIfExists('qb_tokens');
    }
}
