<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181205073031 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_moyens (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE booking');
        $this->addSql('ALTER TABLE moyens CHANGE id_type category_moyens_id INT NOT NULL');
        $this->addSql('ALTER TABLE moyens ADD CONSTRAINT FK_7493DDC962CFC317 FOREIGN KEY (category_moyens_id) REFERENCES category_moyens (id)');
        $this->addSql('CREATE INDEX IDX_7493DDC962CFC317 ON moyens (category_moyens_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE moyens DROP FOREIGN KEY FK_7493DDC962CFC317');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE category_moyens');
        $this->addSql('DROP INDEX IDX_7493DDC962CFC317 ON moyens');
        $this->addSql('ALTER TABLE moyens CHANGE category_moyens_id id_type INT NOT NULL');
    }
}
