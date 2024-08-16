<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240816213948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `content` (
          id INT AUTO_INCREMENT NOT NULL,
          title VARCHAR(255) NOT NULL,
          description VARCHAR(255) NOT NULL,
          rate DOUBLE PRECISION DEFAULT NULL,
          is_favorite TINYINT(1) DEFAULT 0 NOT NULL,
          user_id INT NOT NULL,
          INDEX IDX_FEC530A9A76ED395 (user_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8');
        $this->addSql('CREATE TABLE `user` (
          id INT AUTO_INCREMENT NOT NULL,
          email VARCHAR(180) NOT NULL,
          name VARCHAR(255) NOT NULL,
          roles JSON NOT NULL,
          password VARCHAR(255) NOT NULL,
          UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER
        SET
          utf8');
        $this->addSql('ALTER TABLE
          `content`
        ADD
          CONSTRAINT FK_FEC530A9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `content` DROP FOREIGN KEY FK_FEC530A9A76ED395');
        $this->addSql('DROP TABLE `content`');
        $this->addSql('DROP TABLE `user`');
    }
}
