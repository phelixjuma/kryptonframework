<?php

use Phinx\Migration\AbstractMigration;

class PasswordReset extends AbstractMigration
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
        $table = $this->table('password_reset');

        $table
            ->addColumn('user_id', 'integer')
            ->addColumn("code","integer")
            ->addColumn("request_date", "timestamp", ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn("ip_address", "string", ['limit' => 255, 'null' => true])
            ->addColumn("user_agent", "string", ['limit' => 255, 'null' => true])

            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by', 'integer', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('updated_by', 'integer', ['null' => true])
            ->addColumn('is_archived', 'boolean', ['default' => false])
            ->addColumn('archived_at', 'datetime', ['null' => true])
            ->addColumn('archived_by', 'integer', ['null' => true])

            ->addForeignKey("user_id", "users","id", array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))

            ->create();
    }
}
