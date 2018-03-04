<?php


use Phinx\Migration\AbstractMigration;

class CreateRatingsTable extends AbstractMigration
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
        $table = $this->table('ratings', ['id'=>false, 'primary_key'=>['recipe_id','user_id']]);
        
        $table->addColumn('user_id',    'biginteger', ['signed' => false])
              ->addColumn('recipe_id',  'biginteger', ['signed' => false])
              //->addColumn('rating', 'stars', ['values' => ['1', '2', '3', '4', '5']]) //Nicer, but for MySQL Only
              
              ->addColumn('created_at', 'timestamp',  ['default' => 'CURRENT_TIMESTAMP'])
              // Ratings cannot be overwritten by design, so we won't have updated & deleted Timestamps
               
              ->addForeignKey('user_id',   'users',   'id', ['delete'=>'RESTRICT'])
              ->addForeignKey('recipe_id', 'recipes', 'id', ['delete'=>'RESTRICT'])
              ->create();
              
        /**
         * WARNING: This is specific to PostgreSQL
         * @todo Fix Phinx adapter and send pull request, so we can use the syntax above
         **/
        $this->getAdapter()->execute("CREATE TYPE stars AS ENUM ('1', '2', '3', '4', '5');");
        $this->getAdapter()->execute("ALTER TABLE ratings ADD COLUMN rating stars;");
    }
    
    /**
     * We need the down() method, because Phinx can't handle
     * raw queries in the magic change() method, so we need
     * to manually undo our migration.
     **/ 
    public function down()
    {
        $this->dropTable('ratings');
        $this->getAdapter()->execute("DROP TYPE stars;");
    }
}
