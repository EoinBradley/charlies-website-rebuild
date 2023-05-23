<?php

use Phinx\Migration\AbstractMigration;

class AddUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
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
        $users = $this->table('users');
        $users->addColumn('username', 'string', ['limit' => 100])
            ->addColumn('password', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('first_name', 'string', ['limit' => 100])
            ->addColumn('last_name', 'string', ['limit' => 100])
            ->addColumn('pin', 'string', ['limit' => 4, 'null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('activated_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('actor_id','integer')
            ->addForeignKey('actor_id', 'users', 'id')
            ->addIndex('username', ['unique' => true])
            ->addIndex('email', ['unique' => true])
            ->create();
    }
}
