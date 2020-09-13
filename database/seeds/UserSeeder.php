<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('company')->insert([
            'name' => 'Company A'
        ]);
        DB::table('company')->insert([
            'name' => 'Company B'
        ]);
        DB::table('company')->insert([
            'name' => 'Company C'
        ]);
         DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'company1@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 1,
            'company_id' => 1,
        ]);
		 DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'company2@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 1,
            'company_id' => 2,
        ]);
 		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'company3@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 1,
            'company_id' => 3,
        ]);
 		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'user1@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 0
        ]);
		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'user2@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 0
        ]);
		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'user3@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 0
        ]);

		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'user4@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 0
        ]);
		DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'user5@gmail.com',
            'password' => Hash::make('password'),
            'address' => Str::random(10),
         	'user_type' => 0
        ]);

    }
}
