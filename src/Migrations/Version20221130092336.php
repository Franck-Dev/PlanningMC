<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130092336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prog_moyens ADD code_avion VARCHAR(100) NOT NULL, CHANGE couleur couleur VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_8D93D649A0905086 ON user');
        $this->addSql('ALTER TABLE user ADD prog_avion LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', DROP service_id, DROP poste_id, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prog_moyens DROP code_avion, CHANGE couleur couleur VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F9776AC8');
        $this->addSql('ALTER TABLE user ADD service_id INT NOT NULL, ADD poste_id INT NOT NULL, DROP prog_avion, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE INDEX IDX_8D93D649A0905086 ON user (poste_id)');
    }
}
