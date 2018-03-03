<?php


use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
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
        $table = $this->table('users', ['id'=>false, 'primary_key'=>'id']);
        
        $table->addColumn('id',         'biginteger',  ['identity' => true, 'signed' => false])
              ->addColumn('email',      'string')
              ->addColumn('username',   'string')
              ->addColumn('password',   'string')
              ->addColumn('salt',       'string')
              ->addColumn('name',       'string')
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['null' => true])
              ->addColumn('deleted_at', 'timestamp', ['null' => true])
              
              ->addIndex(['username',   'email'],   ['unique' => true])
              ->create();
    }
}
