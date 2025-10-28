<?php

/**
 * CreateNotificationsTable Migration
 */
class CreateNotificationsTable
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('
            CREATE TABLE notifications (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id int(11) NOT NULL,
                type varchar(50) NOT NULL,
                title varchar(255) NOT NULL,
                message text NOT NULL,
                link varchar(500) DEFAULT NULL,
                icon varchar(100) DEFAULT NULL,
                color varchar(50) DEFAULT NULL,
                read_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_user_id (user_id),
                KEY idx_read_at (read_at),
                KEY idx_created_at (created_at),
                CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES auth_users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('DROP TABLE IF EXISTS notifications');
    }
}
