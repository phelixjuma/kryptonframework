<?php


use Phinx\Seed\AbstractSeed;

class PermissionsSeeder extends AbstractSeed
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
            array('id' => 1, 'name'    => 'create_user', 'description' => 'Create User'),
            array('id' => 2, 'name'    => 'read_user', 'description' => 'Get User'),
            array('id' => 3, 'name'    => 'update_user', 'description' => 'Update User'),
            array('id' => 4, 'name'    => 'delete_user', 'description' => 'Delete User'),
            array('id' => 5, 'name'    => 'create_role', 'description' => 'Create Role'),
            array('id' => 6, 'name'    => 'read_role', 'description' => 'Get Role'),
            array('id' => 7, 'name'    => 'update_role', 'description' => 'Update Role'),
            array('id' => 8, 'name'    => 'delete_role', 'description' => 'Delete Role')
        ];

        $this->table('permissions')->insert($data)->save();

    }
}
