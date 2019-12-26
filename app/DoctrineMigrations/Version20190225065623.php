<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190225065623 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE landing_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_DBDA5B935E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE landing_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, status INT NOT NULL, slug VARCHAR(255) NOT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE landing_block (id INT AUTO_INCREMENT NOT NULL, landing_page_id INT NOT NULL, position INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', status INT DEFAULT 1 NOT NULL, INDEX IDX_31353174DF122DC5 (landing_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE landing_block ADD CONSTRAINT FK_31353174DF122DC5 FOREIGN KEY (landing_page_id) REFERENCES landing_page (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE landing_block DROP FOREIGN KEY FK_31353174DF122DC5');
        $this->addSql('DROP TABLE landing_template');
        $this->addSql('DROP TABLE landing_page');
        $this->addSql('DROP TABLE landing_block');
    }
}
