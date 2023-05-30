<?php

use Phinx\Migration\AbstractMigration;

class UserPermissions extends AbstractMigration
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
        $groups = $this->table('groups');
        $groups->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->create();

        $userGroups = $this->table('group_user');
        $userGroups->addColumn('user_id', 'integer')
            ->addColumn('group_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('user_id', 'users', 'id')
            ->addForeignKey('group_id', 'groups', 'id')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $roles = $this->table('roles');
        $roles->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->create();

        $groupRoles = $this->table('group_role');
        $groupRoles->addColumn('group_id', 'integer')
            ->addColumn('role_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('group_id', 'groups', 'id')
            ->addForeignKey('role_id', 'roles', 'id')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();
    }
}
