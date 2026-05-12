<?php

use yii\db\Migration;

class m260512_083954_initial_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'photo' => $this->string(256),
            'password' => $this->string(256)->notNull(),
            'phone' => $this->string(20)->notNull(),
            'email' => $this->string(256)->notNull(),
            'role' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'ban_status' => $this->integer()->notNull()->defaultValue(0),
            'ban_reason' => $this->text(),
            'ban_until' => $this->dateTime(),
            'violations_count' => $this->integer()->notNull()->defaultValue(0),
        ]);
        
        $this->createTable('countries', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'flag' => $this->string(256)->notNull(),
            'map' => $this->string(256)->notNull(),
            'population' => $this->integer()->notNull(),
            'descr' => $this->text()->notNull(),
            'date_origin' => $this->date()->notNull(),
            'date_end' => $this->date(),
            'capital_id' => $this->integer(),
        ]);
        
        $this->createTable('cities', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'flag' => $this->string(256),
            'population' => $this->integer()->notNull(),
            'descr' => $this->text()->notNull(),
        ]);
        
        $this->createTable('city_countries', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->notNull(),
            'country_id' => $this->integer()->notNull(),
            'x' => $this->decimal(5,2)->defaultValue(50),
            'y' => $this->decimal(5,2)->defaultValue(50),
        ]);
        
        $this->createTable('events', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'img' => $this->string(256),
            'date' => $this->date()->notNull(),
            'descr' => $this->text()->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('openings', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'img' => $this->string(256),
            'date' => $this->date()->notNull(),
            'descr' => $this->text()->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('popular_humans', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'last_name' => $this->string(50)->notNull(),
            'patronymic' => $this->string(100),
            'img' => $this->string(256)->notNull(),
            'type' => $this->string(100)->notNull(),
            'descr' => $this->text()->notNull(),
            'quote' => $this->text(),
            'date_born' => $this->date()->notNull(),
            'date_death' => $this->date(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('vehicles', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'img' => $this->string(256)->notNull(),
            'descr' => $this->text()->notNull(),
            'type' => $this->string(20)->notNull(),
            'status' => $this->string(20)->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('monuments', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'img' => $this->string(256)->notNull(),
            'status' => $this->string(50)->notNull(),
            'descr' => $this->text()->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('weapons', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'img' => $this->string(256)->notNull(),
            'descr' => $this->text()->notNull(),
            'status' => $this->string(256)->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('clothes', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
            'img' => $this->string(256)->notNull(),
            'descr' => $this->text()->notNull(),
            'status' => $this->string(50)->notNull(),
            'countries_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'user_id' => $this->integer(),
            'moderation_status' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ]);
        
        $this->createTable('discussions', [
            'id' => $this->primaryKey(),
            'title' => $this->string(200)->notNull(),
            'content' => $this->text()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'messages_count' => $this->integer()->defaultValue(0),
            'is_admin_only' => $this->integer()->notNull()->defaultValue(0),
        ]);
        
        $this->createTable('comments', [
            'id' => $this->primaryKey(),
            'content' => $this->text()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'cities_id' => $this->integer(),
            'events_id' => $this->integer(),
            'openings_id' => $this->integer(),
            'popular_humans_id' => $this->integer(),
            'vehicles_id' => $this->integer(),
            'weapons_id' => $this->integer(),
            'monuments_id' => $this->integer(),
            'clothes_id' => $this->integer(),
            'discussions_id' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
            'parent_id' => $this->integer(),
            'updated_at' => $this->dateTime(),
        ]);
        
        $this->createTable('applications', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'type' => $this->string(20)->notNull(),
            'descr' => $this->text()->notNull(),
            'file' => $this->string(256),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'answer' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
        
        $this->addForeignKey('fk_city_countries_city', 'city_countries', 'city_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_city_countries_country', 'city_countries', 'country_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_countries_capital', 'countries', 'capital_id', 'cities', 'id', 'SET NULL');
        $this->addForeignKey('fk_events_country', 'events', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_events_city', 'events', 'cities_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_events_user', 'events', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_openings_country', 'openings', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_openings_city', 'openings', 'cities_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_openings_user', 'openings', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_humans_country', 'popular_humans', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_humans_city', 'popular_humans', 'cities_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_humans_user', 'popular_humans', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_vehicles_country', 'vehicles', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_vehicles_city', 'vehicles', 'cities_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_vehicles_user', 'vehicles', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_monuments_country', 'monuments', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_monuments_city', 'monuments', 'cities_id', 'cities', 'id', 'SET NULL');
        $this->addForeignKey('fk_monuments_user', 'monuments', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_weapons_country', 'weapons', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_weapons_city', 'weapons', 'cities_id', 'cities', 'id', 'SET NULL');
        $this->addForeignKey('fk_weapons_user', 'weapons', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_clothes_country', 'clothes', 'countries_id', 'countries', 'id', 'CASCADE');
        $this->addForeignKey('fk_clothes_city', 'clothes', 'cities_id', 'cities', 'id', 'SET NULL');
        $this->addForeignKey('fk_clothes_user', 'clothes', 'user_id', 'users', 'id', 'SET NULL');
        $this->addForeignKey('fk_discussions_user', 'discussions', 'user_id', 'users', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_user', 'comments', 'user_id', 'users', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_parent', 'comments', 'parent_id', 'comments', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_city', 'comments', 'cities_id', 'cities', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_event', 'comments', 'events_id', 'events', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_opening', 'comments', 'openings_id', 'openings', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_human', 'comments', 'popular_humans_id', 'popular_humans', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_vehicle', 'comments', 'vehicles_id', 'vehicles', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_weapon', 'comments', 'weapons_id', 'weapons', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_monument', 'comments', 'monuments_id', 'monuments', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_clothe', 'comments', 'clothes_id', 'clothes', 'id', 'CASCADE');
        $this->addForeignKey('fk_comments_discussion', 'comments', 'discussions_id', 'discussions', 'id', 'CASCADE');
        $this->addForeignKey('fk_applications_user', 'applications', 'user_id', 'users', 'id', 'CASCADE');
        
        $this->createIndex('idx_users_email', 'users', 'email');
        $this->createIndex('idx_cities_name', 'cities', 'name');
        $this->createIndex('idx_discussions_updated', 'discussions', 'updated_at');
        $this->createIndex('idx_comments_created', 'comments', 'created_at');
        $this->createIndex('idx_applications_user', 'applications', 'user_id');
        $this->createIndex('idx_popular_humans_country', 'popular_humans', 'countries_id');
    }

    public function safeDown()
    {
        $this->dropTable('applications');
        $this->dropTable('comments');
        $this->dropTable('discussions');
        $this->dropTable('clothes');
        $this->dropTable('weapons');
        $this->dropTable('monuments');
        $this->dropTable('vehicles');
        $this->dropTable('popular_humans');
        $this->dropTable('openings');
        $this->dropTable('events');
        $this->dropTable('city_countries');
        $this->dropTable('cities');
        $this->dropTable('countries');
        $this->dropTable('users');
    }
}