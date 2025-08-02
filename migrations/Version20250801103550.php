<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801103550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_incident ADD equipement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket_incident ADD CONSTRAINT FK_B86A5D9B806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('CREATE INDEX IDX_B86A5D9B806F0F5C ON ticket_incident (equipement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_incident DROP FOREIGN KEY FK_B86A5D9B806F0F5C');
        $this->addSql('DROP INDEX IDX_B86A5D9B806F0F5C ON ticket_incident');
        $this->addSql('ALTER TABLE ticket_incident DROP equipement_id');
    }
}
