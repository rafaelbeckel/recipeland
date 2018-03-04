<?php


use Phinx\Migration\AbstractMigration;

class CreateAclTables extends AbstractMigration
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
        $table = $this->table('roles', ['id'=>false, 'primary_key'=>'id']);
        
        $table->addColumn('id',           'biginteger', ['identity' => true, 'signed' => false])
              ->addColumn('name',         'string')
              ->addColumn('display_name', 'string',     ['null' => true])
              ->addColumn('description',  'string',     ['null' => true])
              ->addColumn('created_at',   'timestamp',   ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at',   'timestamp',   ['null' => true])
              
              ->addIndex('name', ['unique' => true])
              ->create();
              
        
        $table = $this->table('role_user', ['id'=>false, 'primary_key'=>['user_id','role_id']]);
        
        $table->addColumn('user_id',      'biginteger', ['signed' => false])
              ->addColumn('role_id',      'biginteger', ['signed' => false])
              ->addColumn('created_at',   'timestamp',   ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at',   'timestamp',   ['null' => true])
              
              ->addForeignKey('user_id', 'users', 'id', ['delete'=>'CASCADE'])
              ->addForeignKey('role_id', 'roles', 'id', ['delete'=>'CASCADE'])
              ->create();
        
        
        $table = $this->table('permissions', ['id'=>false, 'primary_key'=>'id']);
        
        $table->addColumn('id',           'biginteger', ['identity' => true, 'signed' => false])
              ->addColumn('name',         'string')
              ->addColumn('display_name', 'string',     ['null' => true])
              ->addColumn('description',  'string',     ['null' => true])
              ->addColumn('created_at',   'datetime',   ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at',   'datetime',   ['null' => true])
              
              ->addIndex('name', ['unique' => true])
              ->create();
              
              
        $table = $this->table('permission_role', ['id'=>false, 'primary_key'=>['permission_id','role_id']]);
        
        $table->addColumn('permission_id', 'biginteger', ['signed' => false])
              ->addColumn('role_id',       'biginteger', ['signed' => false])
              ->addColumn('created_at',    'datetime',   ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at',    'datetime',   ['null' => true])
              
              ->addForeignKey('permission_id', 'permissions', 'id', ['delete'=>'CASCADE'])
              ->addForeignKey('role_id',       'roles',       'id', ['delete'=>'CASCADE'])
              ->create();
    }
}
