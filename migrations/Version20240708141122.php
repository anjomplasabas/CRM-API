<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240708141122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            page_id INT NOT NULL,
            customer_id INT NOT NULL, 
            reference_number VARCHAR(255) NOT NULL,
            total_amount DOUBLE NOT NULL,
            grand_total_amount DOUBLE NOT NULL,
            status VARCHAR(50) NOT NULL, 
            metadata JSON DEFAULT NULL, 
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('CREATE TABLE customers (
            id INT AUTO_INCREMENT NOT NULL, 
            page_id INT NOT NULL,
            facebook_profile_id VARCHAR(255) NOT NULL, 
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(100) DEFAULT NULL, 
            mobile_number VARCHAR(11) DEFAULT NULL,
            address_line VARCHAR(255) NOT NULL,
            region VARCHAR(255) NOT NULL,
            province VARCHAR(255) NOT NULL,
            city VARCHAR(255) NOT NULL,
            barangay VARCHAR(255) NOT NULL,
            zip_code VARCHAR(50) NOT NULL,
            PRIMARY KEY(id)) 
            DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE customers');
    }
}
