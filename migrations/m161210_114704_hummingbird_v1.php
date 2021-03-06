<?php

use yii\db\Migration;

class m161210_114704_hummingbird_v1 extends Migration
{

    public function up()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
          '{{%database}}',
          [
            'id'         => $this->primaryKey()->comment('ID'),
            'alias'      => $this->string(32)->notNull()->unique()->comment('alias of this database'),
            'host'       => $this->string(64)->notNull()->comment('database host'),
            'port'       => $this->integer()->notNull()->defaultValue(3306)->comment('the port of database server'),
            'type'       => $this->string()->notNull()->defaultValue('mysql')->comment('the type of database'),
            'database'   => $this->string(64)->notNull()->comment('database name'),
            'username'   => $this->string(64)->comment('user name'),
            'password'   => $this->string(64)->comment('password'),
            'charset'    => $this->string(32)->comment('charset of database'),
            'deleted'    => $this->boolean()->notNull()->defaultValue(0)->comment('logically delete'),
            'created_at' => $this->dateTime()->defaultValue('1000-01-01 00:00:00'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
          ], $tableOptions
        );

        if (YII_DEBUG) {
            $this->insert('{{%database}}',
                          [
                'id'         => 1,
                'alias'      => 'self',
                'host'       => 'localhost',
                'database'   => 'hummingbird',
                'username'   => 'hummingbird',
                'password'   => '123456',
                'charset'    => 'utf8',
                'deleted'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->createTable(
          '{{%report}}',
          [
            'id'          => $this->primaryKey()->comment('ID'),
            'user_id'     => $this->integer()->notNull()->comment('user who created the report'),
            'database_id' => $this->integer()->notNull()->comment('database config id'),
            'name'        => $this->string(32)->notNull()->comment('report name'),
            'sql'         => $this->text()->notNull()->comment('report sql statement'),
            'deleted'     => $this->boolean()->notNull()->defaultValue(0)->comment('report is deleted'),
            'description' => $this->text()->comment('short description'),
            'created_at'  => $this->dateTime()->defaultValue('1000-01-01 00:00:00')->comment('create time'),
            'updated_at'  => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'FOREIGN KEY (database_id) REFERENCES {{%database}} (id)' .
            (' ON DELETE NO ACTION ON UPDATE NO ACTION'),
          ], $tableOptions
        );

        $this->createIndex('key_name', '{{%report}}', 'name');

        $this->createTable(
          '{{%log}}',
          [
            'id'          => $this->primaryKey()->comment('ID'),
            'user_id'     => $this->integer()->notNull()->comment('user who executed this sql'),
            'database_id' => $this->integer()->notNull()->comment('database config id'),
            'sql'         => $this->text()->notNull()->comment('sql statement'),
            'time_spent'  => $this->double()->notNull()->defaultValue(0.0)->comment('time used'),
            'error_code'  => $this->string(32)->comment('error code of the exception'),
            'created_at'  => $this->dateTime()->defaultValue('1000-01-01 00:00:00'),
            'updated_at'  => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'FOREIGN KEY (database_id) REFERENCES {{%database}} (id)' .
            (' ON DELETE NO ACTION ON UPDATE NO ACTION'),
          ], $tableOptions
        );

        $this->createTable(
          '{{%subscription}}',
          [
            'id'         => $this->primaryKey()->comment('ID'),
            'user_id'    => $this->integer()->notNull()->comment('user id'),
            'report_id'  => $this->integer()->notNull()->comment('report id'),
            'deleted'    => $this->boolean()->notNull()->defaultValue(0)->comment('logically delete'),
            'created_at' => $this->dateTime()->defaultValue('1000-01-01 00:00:00'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'FOREIGN KEY (report_id) REFERENCES {{%report}} (id)' .
            (' ON DELETE NO ACTION ON UPDATE NO ACTION'),
          ], $tableOptions
        );


        $this->addColumn('{{%user}}', 'nickname', $this->string(64)->comment('user display name'));

        $this->insert(
          '{{%user}}', 
          [
            'id'                   => 1,
            'username'             => 'admin',
            'nickname'             => Yii::t('app', 'Administrator'),
            'auth_key'             => 'Ijzc9POowAtBKcLv-EynDfTwiiFFK2ol',
            'password_hash'        => '$2y$13$y37fpqs292nJmyKp4sreA.rlImubnmK2I2t77SmGOol480LA2HbhS', //123456
            'password_reset_token' => 'IBjV_0m-Mq7OFOicyYOjhJoEYPBOrBQZ_' . time(),
            'email'                => 'admin@example.com',
            'status'               => 10,
            'created_at'           => time(),
            'updated_at'           => time(),
        ]);
    }

    public function down()
    {
        $this->delete('{{%user}}', 'id=1');
        $this->dropTable('{{%report}}');
        $this->dropTable('{{%database}}');
    }
}
