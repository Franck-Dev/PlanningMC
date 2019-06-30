<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181011141336 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE caract_moyens (id INT AUTO_INCREMENT NOT NULL, id_cat_moyen INT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cat_moyens (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, nb_moyens INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moyens (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, id_type INT NOT NULL, id_service INT NOT NULL, description VARCHAR(255) DEFAULT NULL, id_types_caracteristiques INT NOT NULL, date_maintenance DATE DEFAULT NULL, operationnel TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prog_polym (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, tps_theo VARCHAR(255) DEFAULT NULL, duree VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', tps_charg VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', tps_decharg VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', id_moyen INT NOT NULL, thermocouples TINYINT(1) NOT NULL, spc TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, id_chef INT NOT NULL, nb_pers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE caract_moyens');
        $this->addSql('DROP TABLE cat_moyens');
        $this->addSql('DROP TABLE moyens');
        $this->addSql('DROP TABLE prog_polym');
        $this->addSql('DROP TABLE services');
    }
}
