<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170802142620 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal_group DROP FOREIGN KEY FK_5506B644667D1AFE');
        $this->addSql('ALTER TABLE goal_group ADD CONSTRAINT FK_5506B644667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_invite DROP FOREIGN KEY FK_1464C807FE54D947');
        $this->addSql('ALTER TABLE group_invite ADD CONSTRAINT FK_1464C807FE54D947 FOREIGN KEY (group_id) REFERENCES goal_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES goal_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751E07D4EF2');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751E07D4EF2 FOREIGN KEY (group_goal_id) REFERENCES goal_group (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal_group DROP FOREIGN KEY FK_5506B644667D1AFE');
        $this->addSql('ALTER TABLE goal_group ADD CONSTRAINT FK_5506B644667D1AFE FOREIGN KEY (goal_id) REFERENCES goal (id)');
        $this->addSql('ALTER TABLE group_invite DROP FOREIGN KEY FK_1464C807FE54D947');
        $this->addSql('ALTER TABLE group_invite ADD CONSTRAINT FK_1464C807FE54D947 FOREIGN KEY (group_id) REFERENCES goal_group (id)');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES goal_group (id)');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751E07D4EF2');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751E07D4EF2 FOREIGN KEY (group_goal_id) REFERENCES goal_group (id)');
    }
}
