<?php

namespace ci4seopro\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeoPro extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'config_key'=>[
                'type'=>'VARCHAR',
                'constraint'=>255,
            ],
            'config_type'=>[
                'type'=>'ENUM',
                'constraint'=>['string','array','boolean','integer','object','json']
            ],
            'description'=[
                'type'=>'TEXT'
            ]
        ]);
        $this->forge->addKey('id',true);
        $this->forge->addKey('config_key');
        $this->forge->createTable('seo_settings');

    }

    public function down()
    {
        $this->forge->dropTable('seo_settings');
    }
}
