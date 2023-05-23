<?php

use Phinx\Migration\AbstractMigration;

class OpeningHourExceptionsTable extends AbstractMigration
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
        $exceptions = $this->table('opening_hour_exceptions');
        $exceptions->addColumn('exception_date', 'date')
            ->addColumn('description','string', ['limit' => 100, 'null' => true])
            ->addColumn('opened_at', 'time', ['null' => true])
            ->addColumn('closed_at', 'time', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $exceptionHistory = $this->table('opening_hour_exception_history', ['id' => false]);
        $exceptionHistory->addColumn('version', 'integer')
            ->addColumn('id', 'integer')
            ->addColumn('exception_date', 'date')
            ->addColumn('description','string', ['limit' => 100, 'null' => true])
            ->addColumn('opened_at', 'time', ['null' => true])
            ->addColumn('closed_at', 'time', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer')
            ->addForeignKey('id', 'opening_hour_exceptions', 'id')
            ->create();

        if ($this->isMigratingUp()) {
            $this->execute(<<< sql
                CREATE TRIGGER opening_hour_exceptions_ai
                    AFTER INSERT
                    ON opening_hour_exceptions
                    FOR EACH ROW
                    INSERT INTO opening_hour_exception_history
                    SELECT 1, opening_time.*
                    FROM opening_hour_exceptions AS opening_time WHERE opening_time.id = NEW.id;
            sql);

            $this->execute(<<< sql
                CREATE TRIGGER opening_hour_exceptions_au
                    AFTER UPDATE
                    ON opening_hour_exceptions
                    FOR EACH ROW
                    INSERT INTO opening_hour_exception_history
                    SELECT (SELECT MAX(version) + 1 FROM opening_hour_exception_history WHERE id = NEW.id),
                           opening_time.*
                    FROM opening_hour_exceptions AS opening_time WHERE opening_time.id = NEW.id;
            sql);
        }
    }
}
