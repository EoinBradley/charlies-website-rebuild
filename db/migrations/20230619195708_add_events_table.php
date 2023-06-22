<?php

use Phinx\Migration\AbstractMigration;

class AddEventsTable extends AbstractMigration
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
        $events = $this->table('events');
        $events->addColumn('artist_id', 'integer')
            ->addColumn('start_at', 'datetime')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('artist_id', 'artists', 'id')
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $eventsHistory = $this->table('events_history', ['id' => false]);
        $eventsHistory->addColumn('version', 'integer')
            ->addColumn('id', 'integer')
            ->addColumn('artist_id', 'integer')
            ->addColumn('start_at', 'datetime')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('id', 'events', 'id')
            ->create();

        if ($this->isMigratingUp()) {
            $this->execute(<<< sql
                CREATE TRIGGER events_ai
                    AFTER INSERT
                    ON events
                    FOR EACH ROW
                    INSERT INTO events_history
                    SELECT 1, a.*
                    FROM events AS a WHERE a.id = NEW.id;
            sql);

            $this->execute(<<< sql
                CREATE TRIGGER events_au
                    AFTER UPDATE
                    ON events
                    FOR EACH ROW
                    INSERT INTO events_history
                    SELECT (SELECT MAX(version) + 1 FROM events_history WHERE id = NEW.id),
                           a.*
                    FROM events AS a WHERE a.id = NEW.id;
            sql);
        }
    }
}
