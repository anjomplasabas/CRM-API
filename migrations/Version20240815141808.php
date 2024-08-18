<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815141808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE pages (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            facebook_page_id varchar(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            facebook_page_access_token text NOT NULL,
            metadata JSON NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            PRIMARY KEY(id),
            UNIQUE KEY unique_user_page (user_id, facebook_page_id))
            DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE report_templates');
    }
}
