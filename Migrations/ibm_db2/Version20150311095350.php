<?php

namespace Innova\CollecticielBundle\Migrations\ibm_db2;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/03/11 09:53:53
 */
class Version20150311095350 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE innova_collecticielbundle_document 
            ADD COLUMN drop_id INTEGER DEFAULT NULL
        ");
        $this->addSql("
            ALTER TABLE innova_collecticielbundle_document 
            ADD CONSTRAINT FK_1C357F0C4D224760 FOREIGN KEY (drop_id) 
            REFERENCES innova_collecticielbundle_drop (id)
        ");
        $this->addSql("
            CREATE INDEX IDX_1C357F0C4D224760 ON innova_collecticielbundle_document (drop_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE innova_collecticielbundle_document 
            DROP FOREIGN KEY FK_1C357F0C4D224760
        ");
        $this->addSql("
            DROP INDEX IDX_1C357F0C4D224760
        ");
        $this->addSql("
            ALTER TABLE innova_collecticielbundle_document 
            DROP COLUMN drop_id
        ");
        $this->addSql("
            CALL SYSPROC.ADMIN_CMD (
                'REORG TABLE innova_collecticielbundle_document'
            )
        ");
    }
}