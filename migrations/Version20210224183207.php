<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224183207 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_CFF65260D725330D ON compte (agence_id)');
        $this->addSql('ALTER TABLE depot ADD user_id INT DEFAULT NULL, ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_47948BBCA76ED395 ON depot (user_id)');
        $this->addSql('CREATE INDEX IDX_47948BBCF2C56620 ON depot (compte_id)');
        $this->addSql('ALTER TABLE transaction ADD user_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL, ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D119EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_723705D1A76ED395 ON transaction (user_id)');
        $this->addSql('CREATE INDEX IDX_723705D119EB6921 ON transaction (client_id)');
        $this->addSql('CREATE INDEX IDX_723705D1F2C56620 ON transaction (compte_id)');
        $this->addSql('ALTER TABLE user ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D725330D ON user (agence_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260D725330D');
        $this->addSql('DROP INDEX IDX_CFF65260D725330D ON compte');
        $this->addSql('ALTER TABLE compte DROP agence_id');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCA76ED395');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCF2C56620');
        $this->addSql('DROP INDEX IDX_47948BBCA76ED395 ON depot');
        $this->addSql('DROP INDEX IDX_47948BBCF2C56620 ON depot');
        $this->addSql('ALTER TABLE depot DROP user_id, DROP compte_id');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D119EB6921');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F2C56620');
        $this->addSql('DROP INDEX IDX_723705D1A76ED395 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D119EB6921 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1F2C56620 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP user_id, DROP client_id, DROP compte_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D725330D');
        $this->addSql('DROP INDEX IDX_8D93D649D725330D ON user');
        $this->addSql('ALTER TABLE user DROP agence_id');
    }
}
