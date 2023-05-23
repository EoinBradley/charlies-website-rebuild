<?php

use Phinx\Migration\AbstractMigration;

class AddConfigsTable extends AbstractMigration
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
        $configs = $this->table('configs', ['id' => false, 'primary_key' => 'id']);
        $configs->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('value', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $configHistory = $this->table('configs_history', ['id' => false]);
        $configHistory->addColumn('version', 'integer')
            ->addColumn('id', 'string', ['limit' => 100])
            ->addColumn('value', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('id', 'configs', 'id')
            ->create();

        if ($this->isMigratingUp()) {
            $this->execute(<<< sql
                CREATE TRIGGER configs_ai
                    AFTER INSERT
                    ON configs
                    FOR EACH ROW
                    INSERT INTO configs_history
                    SELECT 1, config.*
                    FROM configs AS config WHERE config.id = NEW.id;
            sql);

            $this->execute(<<< sql
                CREATE TRIGGER configs_au
                    AFTER UPDATE
                    ON configs
                    FOR EACH ROW
                    INSERT INTO configs_history
                    SELECT (SELECT MAX(version) + 1 FROM configs_history WHERE id = NEW.id),
                           config.*
                    FROM configs AS config WHERE config.id = NEW.id;
            sql);
        }
    }
}
