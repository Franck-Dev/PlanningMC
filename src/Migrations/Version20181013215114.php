<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181013215114 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conf_smenu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, id_smenu1 INT DEFAULT NULL, id_smenu2 INT DEFAULT NULL, id_smenu3 INT DEFAULT NULL, id_smenu4 INT DEFAULT NULL, id_smenu5 INT DEFAULT NULL, id_smenu6 INT DEFAULT NULL, id_smenu7 INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE confapply CHANGE titre_smenu titre_smenu INT NOT NULL, CHANGE titre_menu id_titre_menu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE const_menu DROP id_service');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE conf_smenu');
        $this->addSql('ALTER TABLE confapply CHANGE titre_smenu titre_smenu VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE id_titre_menu titre_menu VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE const_menu ADD id_service INT NOT NULL');
    }
}
