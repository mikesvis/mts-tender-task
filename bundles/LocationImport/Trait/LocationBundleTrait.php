<?php


namespace LocationImportBundle\Trait;


trait LocationBundleTrait
{
    protected $bulkSize = 500;

    /**
     * Вставка данных
     */
    public function insert(array $insertData): void
    {
        $count = 0;
        foreach ($insertData as $entity) {
            $count ++;
            $this->getEntityManager()->persist($entity);
            if (($count % $this->bulkSize) === 0) {
                $this->getEntityManager()->flush();
                $count = 0;
            }
        }
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    /**
     * Очистка таблицы
     */
    public function truncate(): void
    {
        $cmd = $this->getEntityManager()->getClassMetadata($this->getClassName());
        $connection = $this->getEntityManager()->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

}