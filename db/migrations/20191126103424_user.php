<?php

use Phinx\Migration\AbstractMigration;

class User extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // create the table
        $table = $this->table('users');

        $table
            ->addColumn('email_address', 'string',['limit' => 255])
            ->addColumn("phone_number","string", ['limit' => 40, 'null' => true])
            ->addColumn("password", "string", ['limit' => 255])
            ->addColumn("first_name", "string", ['limit' => 255])
            ->addColumn("surname", "string", ['limit' => 255])
            ->addColumn("other_name", "string", ['limit' => 255, 'null' => true])
            ->addColumn("gender", "enum", ['values' => ['male', 'female']])
            ->addColumn("avatar_id", "integer", ['null' => true])
            ->addColumn("date_of_birth", "datetime", ['null' => true])
            ->addColumn("status", "enum", ['values' => ['active','suspended'], 'default' => 'active'])
            ->addColumn("role_id", "integer")

            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('updated_by', 'integer', ['null' => true])
            ->addColumn('is_archived', 'boolean', ['default' => false])
            ->addColumn('archived_at', 'datetime', ['null' => true])
            ->addColumn('archived_by', 'integer', ['null' => true])

            ->addIndex(['email_address'], ['unique' => true])
            ->addIndex(['phone_number'], ['unique' => true])

            ->addForeignKey('role_id', 'roles', 'id', array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))

            ->create();
    }
}
