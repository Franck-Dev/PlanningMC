<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630091258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chargement_outillages (chargement_id INT NOT NULL, outillages_id INT NOT NULL, INDEX IDX_959CEC48B8FBE502 (chargement_id), INDEX IDX_959CEC484D6CE55C (outillages_id), PRIMARY KEY(chargement_id, outillages_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chargement_outillages ADD CONSTRAINT FK_959CEC48B8FBE502 FOREIGN KEY (chargement_id) REFERENCES chargement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chargement_outillages ADD CONSTRAINT FK_959CEC484D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demandes ADD chargement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBBB8FBE502 FOREIGN KEY (chargement_id) REFERENCES chargement (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BD940CBBB8FBE502 ON demandes (chargement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chargement_outillages DROP FOREIGN KEY FK_959CEC48B8FBE502');
        $this->addSql('ALTER TABLE chargement_outillages DROP FOREIGN KEY FK_959CEC484D6CE55C');
        $this->addSql('DROP TABLE chargement_outillages');
        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB8D2A2FD3');
        $this->addSql('ALTER TABLE demandes DROP chargement_id');
        $this->addSql('DROP INDEX idx_bd940cbb8d2a2fd3 ON demandes');
    }
}
