<?php
namespace Database\Seeders;

use App\Services\FirebaseService;
use Illuminate\Database\Seeder;

class UserFirebaseDeleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(FirebaseService $firebase)
    {
        $firebase->deleteFirebaseUser();
    }
}
