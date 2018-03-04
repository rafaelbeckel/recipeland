<?php


use Phinx\Migration\AbstractMigration;

class CreateRecipesTable extends AbstractMigration
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
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('recipes', ['id'=>false, 'primary_key'=>'id']);
        
        
        
        $table->addColumn('id',         'biginteger', ['identity' => true, 'signed' => false])
              ->addColumn('created_by', 'biginteger', ['signed' => false])
              ->addColumn('name',       'string')
              ->addColumn('prep_time',  'integer')
              ->addColumn('vegetarian', 'boolean',   ['default' => false])
              ->addColumn('published',  'boolean',   ['default' => true])
              ->addColumn('picture',    'string')
              //->addColumn('difficulty', 'skill_lvl', ['values' => ['1', '2', '3']]) // Nicer, but for MySQL Only
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['null' => true])
              ->addColumn('deleted_at', 'timestamp', ['null' => true])
              
              ->addIndex('name', ['unique' => true])
              ->addForeignKey('created_by', 'users', 'id', ['delete'=>'RESTRICT'])
              ->create();
              
        /**
         * WARNING: This is specific to PostgreSQL
         * @todo Fix Phinx adapter and send pull request
         **/
        $this->getAdapter()->execute("CREATE TYPE skill_lvl AS ENUM ('1', '2', '3');");
        $this->getAdapter()->execute("ALTER TABLE recipes ADD COLUMN difficulty skill_lvl;");
    }
    
    /**
     * We need the down() method, because Phinx can't handle
     * raw queries in the magic change() method, so we need
     * to manually undo our migration.
     **/ 
    public function down()
    {
        $this->dropTable('recipes');
        $this->getAdapter()->execute("DROP TYPE skill_lvl;");
    }
}