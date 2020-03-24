<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170613131031 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal ADD origin_goal_id INT DEFAULT NULL, ADD predefined TINYINT(1) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2ED30A0F93 FOREIGN KEY (origin_goal_id) REFERENCES goal (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FCDCEB2ED30A0F93 ON goal (origin_goal_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2ED30A0F93');
        $this->addSql('DROP INDEX IDX_FCDCEB2ED30A0F93 ON goal');
        $this->addSql('ALTER TABLE goal DROP origin_goal_id, DROP predefined');
    }
}
