<?php


use Phinx\Seed\AbstractSeed;

class RolePermissionsSeeder extends AbstractSeed
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
            array('role_id' => 1, 'permission_id'    => 1),
            array('role_id' => 1, 'permission_id'    => 2),
            array('role_id' => 1, 'permission_id'    => 3),
            array('role_id' => 1, 'permission_id'    => 4),
            array('role_id' => 1, 'permission_id'    => 5),
            array('role_id' => 1, 'permission_id'    => 6),
            array('role_id' => 1, 'permission_id'    => 7),
            array('role_id' => 1, 'permission_id'    => 8),

            array('role_id' => 2, 'permission_id'    => 2)
        ];

        $this->table('roles_permissions')->insert($data)->save();

    }
}
