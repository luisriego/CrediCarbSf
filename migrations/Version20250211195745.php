<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211195745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD type_id CHAR(36) DEFAULT NULL, ADD authority_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75C54C8C93 FOREIGN KEY (type_id) REFERENCES certification_type_entity (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7581EC865B FOREIGN KEY (authority_id) REFERENCES certification_authority (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75C54C8C93 ON certification (type_id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D7581EC865B ON certification (authority_id)');
        $this->addSql('ALTER TABLE certification_type_entity ADD certification_authority_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE certification_type_entity ADD CONSTRAINT FK_9C73A073233A7BA6 FOREIGN KEY (certification_authority_id) REFERENCES certification_authority (id)');
        $this->addSql('CREATE INDEX IDX_9C73A073233A7BA6 ON certification_type_entity (certification_authority_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification_type_entity DROP FOREIGN KEY FK_9C73A073233A7BA6');
        $this->addSql('DROP INDEX IDX_9C73A073233A7BA6 ON certification_type_entity');
        $this->addSql('ALTER TABLE certification_type_entity DROP certification_authority_id');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75C54C8C93');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7581EC865B');
        $this->addSql('DROP INDEX IDX_6C3C6D75C54C8C93 ON certification');
        $this->addSql('DROP INDEX IDX_6C3C6D7581EC865B ON certification');
        $this->addSql('ALTER TABLE certification DROP type_id, DROP authority_id');
    }
}
