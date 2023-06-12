<?php

use Phinx\Migration\AbstractMigration;

class AddArtistTable extends AbstractMigration
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
        $artists = $this->table('artists');
        $artists->addColumn('name', 'string', ['limit' => 245])
            ->addColumn('description', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('actor_id', 'users', 'id')
            ->create();

        $artistHistory = $this->table('artists_history', ['id' => false]);
        $artistHistory->addColumn('version', 'integer')
            ->addColumn('id', 'integer')
            ->addColumn('name', 'string', ['limit' => 245])
            ->addColumn('description', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addColumn('actor_id', 'integer', ['null' => true])
            ->addForeignKey('id', 'artists', 'id')
            ->create();

        if ($this->isMigratingUp()) {
            $this->execute(<<< sql
                CREATE TRIGGER artists_ai
                    AFTER INSERT
                    ON artists
                    FOR EACH ROW
                    INSERT INTO artists_history
                    SELECT 1, a.*
                    FROM artists AS a WHERE a.id = NEW.id;
            sql);

            $this->execute(<<< sql
                CREATE TRIGGER artists_au
                    AFTER UPDATE
                    ON artists
                    FOR EACH ROW
                    INSERT INTO artists_history
                    SELECT (SELECT MAX(version) + 1 FROM artists_history WHERE id = NEW.id),
                           a.*
                    FROM artists AS a WHERE a.id = NEW.id;
            sql);
        }
    }
}
