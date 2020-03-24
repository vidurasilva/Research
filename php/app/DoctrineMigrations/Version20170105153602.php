<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170105153602 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal ADD duration_unit VARCHAR(255) DEFAULT NULL');

		/*
		 * Data in duration column is stored as a string with the following format: '4 weeks'
		 * To split this data into two columns, we move the duration (e.g. 4) and the duration_unit (e.g. weeks) to a new table
		 * Then we insert the data into the goal table and drop the temporary table
		 */
		$this->addSql('CREATE TEMPORARY TABLE `swapt` (
						  `id` INT(11) NOT NULL AUTO_INCREMENT,
						  `duration` INT(11),
						  `duration_unit` varchar(255),
						  PRIMARY KEY (`id`)
						)');
		$this->addSql('INSERT INTO swapt 
							SELECT id, SUBSTRING_INDEX(SUBSTRING_INDEX(duration, \' \',  1), \' \', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(duration, \' \',  2), \' \', -1) 
							FROM goal');
		$this->addSql('UPDATE goal SET duration_unit = (SELECT duration_unit FROM swapt WHERE goal.id = swapt.id)');

		//Cleanup old duration units
		$this->addSql('UPDATE goal SET duration_unit = \'month\' WHERE duration_unit REGEXP \'months\'');
		$this->addSql('UPDATE goal SET duration_unit = \'week\' WHERE duration_unit REGEXP \'day|days|bladiebla|weeks|times\';');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goal DROP duration_unit');
    }
}
