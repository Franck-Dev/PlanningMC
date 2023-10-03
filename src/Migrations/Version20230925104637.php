<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925104637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE changement_outillages (id INT AUTO_INCREMENT NOT NULL, prod_impacte_id INT DEFAULT NULL, prog_id INT DEFAULT NULL, date_deb DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modifed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A7C6C95A259A603D (prod_impacte_id), INDEX IDX_A7C6C95A137D909B (prog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE changement_outillages ADD CONSTRAINT FK_A7C6C95A259A603D FOREIGN KEY (prod_impacte_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE changement_outillages ADD CONSTRAINT FK_A7C6C95A137D909B FOREIGN KEY (prog_id) REFERENCES prog_moyens (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE changement_outillages DROP FOREIGN KEY FK_A7C6C95A259A603D');
        $this->addSql('ALTER TABLE changement_outillages DROP FOREIGN KEY FK_A7C6C95A137D909B');
        $this->addSql('DROP TABLE changement_outillages');
    }
}
