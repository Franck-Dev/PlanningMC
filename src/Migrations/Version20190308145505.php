<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308145505 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE demandes ADD moyen_utilise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demandes ADD CONSTRAINT FK_BD940CBB8D2A2FD3 FOREIGN KEY (moyen_utilise_id) REFERENCES moyens (id)');
        $this->addSql('CREATE INDEX IDX_BD940CBB8D2A2FD3 ON demandes (moyen_utilise_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE demandes DROP FOREIGN KEY FK_BD940CBB8D2A2FD3');
        $this->addSql('DROP INDEX IDX_BD940CBB8D2A2FD3 ON demandes');
        $this->addSql('ALTER TABLE demandes DROP moyen_utilise_id');
    }
}
