<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230627081152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moulage (id INT AUTO_INCREMENT NOT NULL, outillage_id INT DEFAULT NULL, deb_moul DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', fin_moul DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_limite_moulage DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_limite_polym DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', mouleur VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_901CA1701714266E (outillage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moulage ADD CONSTRAINT FK_901CA1701714266E FOREIGN KEY (outillage_id) REFERENCES outillages (id)');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA4346F340C96');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA43491AD7C17 FOREIGN KEY (moulage_id) REFERENCES moulage (id)');
        $this->addSql('CREATE INDEX IDX_556BA43491AD7C17 ON charge (moulage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA43491AD7C17');
        $this->addSql('ALTER TABLE moulage DROP FOREIGN KEY FK_901CA1701714266E');
        $this->addSql('DROP TABLE moulage');
        $this->addSql('DROP INDEX IDX_556BA43491AD7C17 ON charge');
        $this->addSql('ALTER TABLE charge DROP FOREIGN KEY FK_556BA4346F340C96');
        $this->addSql('DROP INDEX idx_556ba4346f340c96 ON charge');
    }
}
