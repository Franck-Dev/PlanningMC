<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220831082055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE charge ADD demandes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA434F49DCC2D FOREIGN KEY (demandes_id) REFERENCES demandes (id)');
        $this->addSql('CREATE INDEX IDX_556BA434F49DCC2D ON charge (demandes_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA4346F340C96');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA434F49DCC2D');
        $this->addSql('DROP INDEX IDX_556BA434F49DCC2D ON charge');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA4346F340C96');
        $this->addSql('ALTER TABLE charge DROP demandes_id');
        $this->addSql('DROP INDEX idx_556ba4346f340c96 ON charge');
    }
}
