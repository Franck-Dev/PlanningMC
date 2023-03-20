<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317141025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prog_avions (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, iri VARCHAR(255) NOT NULL, id_api INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prog_moyens_prog_avions (prog_moyens_id INT NOT NULL, prog_avions_id INT NOT NULL, INDEX IDX_930CBD25C9ECABC3 (prog_moyens_id), INDEX IDX_930CBD258A69CD7F (prog_avions_id), PRIMARY KEY(prog_moyens_id, prog_avions_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prog_moyens_prog_avions ADD CONSTRAINT FK_930CBD25C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prog_moyens_prog_avions ADD CONSTRAINT FK_930CBD258A69CD7F FOREIGN KEY (prog_avions_id) REFERENCES prog_avions (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prog_moyens_prog_avions DROP FOREIGN KEY FK_930CBD25C9ECABC3');
        $this->addSql('ALTER TABLE prog_moyens_prog_avions DROP FOREIGN KEY FK_930CBD258A69CD7F');
        $this->addSql('DROP TABLE prog_avions');
        $this->addSql('DROP TABLE prog_moyens_prog_avions');
    }
}
