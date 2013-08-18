<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAccessTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oauth_access_tokens', function(Blueprint $table)
		{
      $table->string('id', 40)->unique();
      $table->string('client_id', 40);
      $table->integer('user_id')->nullable();
      $table->timestamp('expires');
      $table->text('scope')->nullable();
      $table->primary('id');


      $table->timestamps();

      $table->foreign('client_id')
        ->references('id')
        ->on('oauth_clients')
        ->onDelete('no action')
        ->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::drop('oauth_access_tokens');
	}

}