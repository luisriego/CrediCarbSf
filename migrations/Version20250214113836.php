<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214113836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_cart ADD owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE shopping_cart ADD CONSTRAINT FK_72AAD4F67E3C61F9 FOREIGN KEY (owner_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_72AAD4F67E3C61F9 ON shopping_cart (owner_id)');
        $this->addSql('ALTER TABLE shopping_cart_item ADD shopping_cart_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE shopping_cart_item ADD CONSTRAINT FK_E59A1DF445F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id)');
        $this->addSql('CREATE INDEX IDX_E59A1DF445F80CD ON shopping_cart_item (shopping_cart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_cart DROP FOREIGN KEY FK_72AAD4F67E3C61F9');
        $this->addSql('DROP INDEX IDX_72AAD4F67E3C61F9 ON shopping_cart');
        $this->addSql('ALTER TABLE shopping_cart DROP owner_id');
        $this->addSql('ALTER TABLE shopping_cart_item DROP FOREIGN KEY FK_E59A1DF445F80CD');
        $this->addSql('DROP INDEX IDX_E59A1DF445F80CD ON shopping_cart_item');
        $this->addSql('ALTER TABLE shopping_cart_item DROP shopping_cart_id');
    }
}
