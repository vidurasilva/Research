<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170707090713 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751667D1AFE');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_goal DROP FOREIGN KEY FK_865DA7E7667D1AFE');
        $this->addSql('ALTER TABLE user_goal ADD CONSTRAINT FK_865DA7E7667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751667D1AFE');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE user_goal DROP FOREIGN KEY FK_865DA7E7667D1AFE');
        $this->addSql('ALTER TABLE user_goal ADD CONSTRAINT FK_865DA7E7667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
    }
}
