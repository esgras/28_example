<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181215134556 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE common_question (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber (id INT AUTO_INCREMENT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX email_index (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, status INT DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', hash VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, age INT DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, aim LONGTEXT DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, started DATETIME DEFAULT NULL, INDEX IDX_8D93D6494584665A (product_id), UNIQUE INDEX email_index (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image_file VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, fire INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, author_name VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, link_preview VARCHAR(255) NOT NULL, minutes INT NOT NULL, seconds INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, days INT NOT NULL, price NUMERIC(16, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494584665A');
        $this->addSql('DROP TABLE common_question');
        $this->addSql('DROP TABLE subscriber');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE product');
    }
}
