<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211118084749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE agenda ADD nom_orga_w_id INT NOT NULL');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8774745CFFE FOREIGN KEY (nom_orga_w_id) REFERENCES orga_eq (id)');
        $this->addSql('CREATE INDEX IDX_2CEDC8774745CFFE ON agenda (nom_orga_w_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC8774745CFFE');
        $this->addSql('DROP INDEX IDX_2CEDC8774745CFFE ON agenda');
        $this->addSql('ALTER TABLE agenda DROP nom_orga_w_id');        
    }
}
