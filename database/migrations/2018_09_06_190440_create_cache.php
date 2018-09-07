<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use CareSet\Zermelo\Console\ZermeloInstallCommand;

class CreateCache extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zermelo_cache_meta', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'key' );
            $table->string( 'meta_key' );
            $table->text('meta_value');
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
        Schema::dropIfExists('zermelo_cache_meta');
    }
}
