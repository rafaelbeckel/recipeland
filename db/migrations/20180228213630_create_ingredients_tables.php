<?php


use Phinx\Migration\AbstractMigration;

class CreateIngredientsTables extends AbstractMigration
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
        $table = $this->table('ingredients', ['id'=>false, 'primary_key'=>'id']);
        
        $table->addColumn('id',         'biginteger', ['identity' => true, 'signed' => false])
              ->addColumn('slug',       'string')
              ->addColumn('name',       'string')
              ->addColumn('picture',    'string')
              ->addColumn('allergens',  'string',    ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['null' => true])
              ->addColumn('deleted_at', 'timestamp', ['null' => true])
              
              ->addIndex('slug', ['unique' => true])
              ->addIndex('name', ['unique' => true])
              ->create();
              
              
        $table = $this->table('recipe_ingredient', ['id'=>false, 'primary_key'=>['recipe_id','ingredient_id']]);
        
        $table->addColumn('recipe_id',     'biginteger', ['signed' => false])
              ->addColumn('ingredient_id', 'biginteger', ['signed' => false])
              ->addColumn('quantity',      'string')
              ->addColumn('unit',          'string')
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addColumn('deleted_at', 'datetime', ['null' => true])
              
              ->addForeignKey('recipe_id',     'recipes',     'id', ['delete' => 'CASCADE'])
              ->addForeignKey('ingredient_id', 'ingredients', 'id', ['delete' => 'CASCADE'])
              ->create();
    }
}
