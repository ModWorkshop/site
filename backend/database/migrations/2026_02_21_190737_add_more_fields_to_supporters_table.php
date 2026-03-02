<?php

use App\Models\SupporterPackage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supporters', function (Blueprint $table) {
            $table->integer('price')->default(0);

            $table->bigInteger('supporter_package_id')->unsigned()->nullable();
            $table->foreign('supporter_package_id')->references('id')->on('supporter_packages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supporters', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('supporter_package_id');
        });
    }
};
