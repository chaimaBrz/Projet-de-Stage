<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250726132723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alerte (id INT AUTO_INCREMENT NOT NULL, equipement_id INT NOT NULL, titre VARCHAR(255) NOT NULL, niveau INT NOT NULL, message LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_3AE753A806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, fournisseur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, etat INT NOT NULL, dateinstallation DATETIME NOT NULL, INDEX IDX_B8B4C6F3C54C8C93 (type_id), INDEX IDX_B8B4C6F3670C757F (fournisseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_historique (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, ticket_incident_id INT NOT NULL, date DATETIME NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_1CC569A2C54C8C93 (type_id), INDEX IDX_1CC569A2357539C6 (ticket_incident_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fournisseur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventaire (id INT AUTO_INCREMENT NOT NULL, etat VARCHAR(255) NOT NULL, date DATETIME NOT NULL, reference VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, commentaire LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_incident (id INT AUTO_INCREMENT NOT NULL, user_createur_id INT NOT NULL, user_assigne_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, INDEX IDX_B86A5D9BDAB9C870 (user_createur_id), INDEX IDX_B86A5D9B291C4C5C (user_assigne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alerte ADD CONSTRAINT FK_3AE753A806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE evenement_historique ADD CONSTRAINT FK_1CC569A2C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE evenement_historique ADD CONSTRAINT FK_1CC569A2357539C6 FOREIGN KEY (ticket_incident_id) REFERENCES ticket_incident (id)');
        $this->addSql('ALTER TABLE ticket_incident ADD CONSTRAINT FK_B86A5D9BDAB9C870 FOREIGN KEY (user_createur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_incident ADD CONSTRAINT FK_B86A5D9B291C4C5C FOREIGN KEY (user_assigne_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte DROP FOREIGN KEY FK_3AE753A806F0F5C');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3C54C8C93');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3670C757F');
        $this->addSql('ALTER TABLE evenement_historique DROP FOREIGN KEY FK_1CC569A2C54C8C93');
        $this->addSql('ALTER TABLE evenement_historique DROP FOREIGN KEY FK_1CC569A2357539C6');
        $this->addSql('ALTER TABLE ticket_incident DROP FOREIGN KEY FK_B86A5D9BDAB9C870');
        $this->addSql('ALTER TABLE ticket_incident DROP FOREIGN KEY FK_B86A5D9B291C4C5C');
        $this->addSql('DROP TABLE alerte');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE evenement_historique');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE inventaire');
        $this->addSql('DROP TABLE ticket_incident');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
    }
}
