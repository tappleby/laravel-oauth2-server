<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAuthorizationCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oauth_authorization_codes', function(Blueprint $table)
			{
				$table->string('id', 40)->unique();
				$table->string('client_id', 40);
				$table->integer('user_id')->nullable();
				$table->timestamp('expires');
				$table->text('scope')->nullable();
				$table->text('redirect_uri')->nullable();
				$table->primary('id');


				$table->timestamps();

				$table->foreign('client_id')
					->references('id')
					->on('oauth_clients')
					->onDelete('cascade')
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
		Schema::drop('oauth_authorization_codes');
	}

}