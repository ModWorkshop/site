<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::orderBy('id')->get();
        
        foreach ($users as $user) {
            //We want to make sure the name of the user is friendly for URLs
            $name = Str::match('/[a-z0-9_-]+/', Str::replace('/\s+/u', '-', Str::lower($user->name)));
            $finalName = $name;
            $num = 0;

            //Well... seems like the user has a problematic name, let's generate a random name.
            if (strlen($finalName) < 3) {
                $finalName = Str::random();
            }

            //Find unique names
            while($userTest = User::where('unique_name', $finalName)->first('unique_name')) {
                $num++;
                $finalName = $name.$num;
                unset($userTest);
            }
   
            echo $finalName.' ';

            $user->update(['unique_name' => $finalName]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
