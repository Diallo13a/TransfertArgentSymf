<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210319004756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE summarize_transaction ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE summarize_transaction ADD CONSTRAINT FK_5A4A925AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A4A925AA76ED395 ON summarize_transaction (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE summarize_transaction DROP FOREIGN KEY FK_5A4A925AA76ED395');
        $this->addSql('DROP INDEX IDX_5A4A925AA76ED395 ON summarize_transaction');
        $this->addSql('ALTER TABLE summarize_transaction DROP user_id');
    }
}
