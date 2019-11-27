<?php


use Phinx\Seed\AbstractSeed;
use Kuza\Krypton\Classes\Utils;
use Kuza\Krypton\Classes\Dates;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $data = [
            "id"            => 1,
            'email_address' => 'admin@kryptonframework.com',
            "phone_number"  => $faker->phoneNumber,
            'password'      => Utils::hashPassword('1234'),
            'first_name'    => $faker->firstName,
            'surname'     => $faker->lastName,
            'gender'        => 'male',
            'date_of_birth' => Dates::formatDate('1994-01-01','Y-m-d'),
            'role_id'       => 1
        ];

        $this->table('users')->insert($data)->save();
    }
}
