<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170802142934 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE milestone DROP FOREIGN KEY FK_4FAC8382667D1AFE');
        $this->addSql('ALTER TABLE milestone ADD CONSTRAINT FK_4FAC8382667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE milestone DROP FOREIGN KEY FK_4FAC8382667D1AFE');
        $this->addSql('ALTER TABLE milestone ADD CONSTRAINT FK_4FAC8382667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
    }
}
