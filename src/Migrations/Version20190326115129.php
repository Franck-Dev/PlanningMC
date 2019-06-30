<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326115129 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE prog_moyens CHANGE couleur couleur LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE planning ADD num_demande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6FFE1E93 FOREIGN KEY (num_demande_id) REFERENCES demandes (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D499BFF6FFE1E93 ON planning (num_demande_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6FFE1E93');
        $this->addSql('DROP INDEX UNIQ_D499BFF6FFE1E93 ON planning');
        $this->addSql('ALTER TABLE planning DROP num_demande_id');
        $this->addSql('ALTER TABLE prog_moyens CHANGE couleur couleur VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
