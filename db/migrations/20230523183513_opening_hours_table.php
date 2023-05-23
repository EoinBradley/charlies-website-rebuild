<?php

use Phinx\Migration\AbstractMigration;

class OpeningHoursTable extends AbstractMigration
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
        $openTimes = $this->table('opening_hours');
        $openTimes->addColumn('day_of_week', 'smallinteger')
            ->addColumn('opened_at', 'time', ['null' => true])
            ->addColumn('closed_at', 'time', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $openTimeHistory = $this->table('opening_hour_history', ['id' => false]);
        $openTimeHistory->addColumn('version', 'integer')
            ->addColumn('id', 'integer')
            ->addColumn('day_of_week', 'smallinteger')
            ->addColumn('opened_at', 'time', ['null' => true])
            ->addColumn('closed_at', 'time', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('id', 'opening_hours', 'id')
            ->create();

        if ($this->isMigratingUp()) {
            $this->execute(<<< sql
                CREATE TRIGGER opening_hours_ai
                    AFTER INSERT
                    ON opening_hours
                    FOR EACH ROW
                    INSERT INTO opening_hour_history
                    SELECT 1, opening_time.*
                    FROM opening_hours AS opening_time WHERE opening_time.id = NEW.id;
            sql);

            $this->execute(<<< sql
                CREATE TRIGGER opening_hours_au
                    AFTER UPDATE
                    ON opening_hours
                    FOR EACH ROW
                    INSERT INTO opening_hour_history
                    SELECT (SELECT MAX(version) + 1 FROM opening_hour_history WHERE id = NEW.id),
                           opening_time.*
                    FROM opening_hours AS opening_time WHERE opening_time.id = NEW.id;
            sql);
        }
    }
}
