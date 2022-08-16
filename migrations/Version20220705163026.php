<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705163026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, subtype INT NOT NULL, counting_sum_type INT NOT NULL, number VARCHAR(255) NOT NULL, issue_date DATETIME NOT NULL, sale_date DATETIME NOT NULL, due_date DATETIME NOT NULL, payment_date DATETIME DEFAULT NULL, payment_amount DOUBLE PRECISION DEFAULT NULL, currency VARCHAR(3) NOT NULL, exchange DOUBLE PRECISION NOT NULL, payment_type VARCHAR(255) NOT NULL, language VARCHAR(30) DEFAULT NULL, template INT NOT NULL, issuer_name VARCHAR(255) NOT NULL, receiver_name VARCHAR(255) NOT NULL, order_number INT NOT NULL, department VARCHAR(30) NOT NULL, send_mail INT NOT NULL, storehouse INT NOT NULL, auto_doc_create INT NOT NULL, remarks VARCHAR(255) DEFAULT NULL, additional_remarks VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invoice');
    }
}
