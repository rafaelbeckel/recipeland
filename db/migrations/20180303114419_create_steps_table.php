<?php


use Phinx\Migration\AbstractMigration;

class CreateStepsTable extends AbstractMigration
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
    public function change()
    {
        $table = $this->table('steps', ['id'=>false, 'primary_key'=>'id']);
        
        $table->addColumn('id',          'biginteger', ['identity' => true, 'signed' => false])
              ->addColumn('description', 'string')
              ->addColumn('picture',     'string',     ['null' => true])
              ->addColumn('created_at',  'timestamp',  ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at',  'timestamp',  ['null' => true])
              ->addColumn('deleted_at',  'timestamp',  ['null' => true])
              ->create();
              
              
        $table = $this->table('recipe_step', ['id'=>false, 'primary_key'=>['recipe_id','step_id']]);
        
        $table->addColumn('recipe_id',  'biginteger', ['signed' => false])
              ->addColumn('step_id',    'biginteger', ['signed' => false])
              ->addColumn('order',      'integer')
              ->addColumn('created_at', 'timestamp',  ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp',  ['null' => true])
              ->addColumn('deleted_at', 'timestamp',  ['null' => true])
              
              ->addForeignKey('recipe_id', 'recipes', 'id', ['delete'=>'CASCADE'])
              ->addForeignKey('step_id',   'steps',   'id', ['delete'=>'CASCADE'])
              ->addIndex(['recipe_id', 'order'], ['unique' => true])
              ->create();
    }
}
