<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            //Note: changed password & email to be nullable to allow for social login
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->char('custom_color', 7)->default('');
            $table->rememberToken();

            $table->text('avatar')->default('');

            /**
             * Will be used for mentions and profile name, while this might create some small issues, this also adds a lot of benefits
             * !!THIS IS NOT USED FOR LOGING INTO ACCOUNTS!!
             */
            $table->string('unique_name')->nullable()->unique();

            $table->string('banner')->default('');
            $table->text('bio')->default('');
            $table->boolean('private_profile')->default(false);
            $table->tinyText('custom_title')->default('');
            $table->boolean('invisible')->default(false);
            $table->tinyText('donation_url')->nullable();

            $table->timestamp('last_online')->nullable();

            $table->enum('show_tag', [
                'role',
                'supporter_or_role',
                'none'
            ])->default('supporter_or_role');

            $table->ipAddress('last_ip_address')->nullable();

            $table->index('unique_name');
            $table->index('name');

            $table->boolean('activated')->default(false);

            $table->string('pending_email')->nullable()->unique();
            $table->timestamp('pending_email_set_at')->nullable();

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
        Schema::dropIfExists('users');
    }
}
