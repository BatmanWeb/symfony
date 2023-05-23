<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220121103021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD tva_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E664D79775F FOREIGN KEY (tva_id) REFERENCES tva (id)');
        $this->addSql('CREATE INDEX IDX_23A0E664D79775F ON article (tva_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E664D79775F');
        $this->addSql('DROP INDEX IDX_23A0E664D79775F ON article');
        $this->addSql('ALTER TABLE article DROP tva_id');
    }
}
