<?php


use Phinx\Seed\AbstractSeed;

class RolesSeeder extends AbstractSeed
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
        $data = [
            array('id' => 1, 'name'    => 'Super Admin', 'type' => 'admin'),
            array('id' => 2, 'name'    => 'User', 'type' => 'user')
        ];

        $this->table('roles')->insert($data)->save();
    }
}
