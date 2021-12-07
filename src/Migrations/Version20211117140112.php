<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117140112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE nom_equipe ADD CONSTRAINT FK_A4862542D3B4BEBF FOREIGN KEY (orga_w_id) REFERENCES types_equipe (id)');
        $this->addSql('ALTER TABLE nom_equipe ADD CONSTRAINT FK_A4862542783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orga_eq ADD CONSTRAINT FK_5698C346F9776AC8 FOREIGN KEY (nom_equipe_id) REFERENCES nom_equipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE nom_equipe DROP FOREIGN KEY FK_A4862542D3B4BEBF');
        $this->addSql('ALTER TABLE nom_equipe DROP FOREIGN KEY FK_A4862542783E3463');
        $this->addSql('ALTER TABLE orga_eq DROP FOREIGN KEY FK_5698C346F9776AC8');
    }
}
