<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSocketsAndWrenches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop everything and re-create. This avoids DBAL dependency for renaming columns
        Schema::dropIfExists('socket');
        Schema::dropIfExists('socketsource');
        Schema::dropIfExists('socket_user');
        Schema::dropIfExists('wrench');
        //
//        CREATE TABLE `socket` (
//    `id` int(11) NOT NULL,
//  `wrench_id` int(11) NOT NULL,
//  `wrench_value` varchar(1000) NOT NULL,
//  `wrench_label` varchar(1000) NOT NULL,
//  `is_default_socket` tinyint(4) NOT NULL DEFAULT 0,
//  `socketsource_id` int(11) NOT NULL,
//  `created_at` datetime NOT NULL,
//  `updated_at` datetime NOT NULL
//) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        Schema::create('socket', function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'wrench_id' );
            $table->string( 'socket_value', 1024 );
            $table->string('socket_label', 1024);
            $table->boolean( 'is_default_socket' );
            $table->integer( 'socketsource_id');
            $table->timestamps();
        });
//
//        CREATE TABLE `socketsource` (
//    `id` int(11) NOT NULL,
//  `socketsource_name` varchar(255) NOT NULL,
//  `created_at` datetime NOT NULL,
//  `updated_at` datetime NOT NULL
//) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        Schema::create('socketsource', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'socketsource_name', 1024 );
            $table->timestamps();
        });
//
//        CREATE TABLE `socket_user` (
//    `id` int(11) NOT NULL,
//  `user_id` int(11) NOT NULL,
//  `wrench_id` int(11) NOT NULL,
//  `current_chosen_socket_id` int(11) NOT NULL,
//  `created_at` datetime NOT NULL,
//  `updated_at` datetime NOT NULL
//) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        Schema::create('socket_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('wrench_id');
            $table->integer('current_chosen_socket_id');
            $table->timestamps();
        });

//
//        CREATE TABLE `wrench` (
//    `id` int(11) NOT NULL,
//  `wrench_lookup_string` varchar(255) NOT NULL,
//  `wrench_label` varchar(255) NOT NULL,
//  `created_at` datetime NOT NULL,
//  `updated_at` datetime NOT NULL
//) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        Schema::create('wrench', function (Blueprint $table) {
            $table->increments('id');
            $table->string( 'wrench_lookup_string', 1024 );
            $table->string( 'wrench_label', 1024 );
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
        //
        Schema::dropIfExists('socket');
        Schema::dropIfExists('socketsource');
        Schema::dropIfExists('socket_user');
        Schema::dropIfExists('wrench');
    }
}
