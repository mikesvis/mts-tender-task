<?php


namespace LocationImportBundle\Service;


use App\Repository\RegionRepository;
use LocationImportBundle\Handler\HandlerFactory;
use LocationImportBundle\Handler\Mapper;

class ServiceRegion extends AbstractServiceLocation
{
    /** @var string  */
    protected string $resourceRegion;

    /** @var Mapper  */
    protected Mapper $mapperRegion;

    /** @var RegionRepository  */
    protected RegionRepository $repositoryRegion;

    /**
     * ServiceRegion конструктор
     *
     * @param string $resourceRegion Источних входных данных для регионов
     * @param RegionRepository $repositoryRegion Репозиторий для работы с сущностями из базы
     * @param HandlerFactory $handlerFactory Фабрика обработчиков входных данных
     */
    public function __construct(
        string $resourceRegion,
        RegionRepository $repositoryRegion,
        HandlerFactory $handlerFactory
    )
    {
        $this->resourceRegion = $resourceRegion;
        $this->repositoryRegion = $repositoryRegion;
        $this->parser = $handlerFactory->getParser();
        $this->mapperRegion = $handlerFactory->getMapper();
    }

    /**
     * Очистить таблицу регионов
     */
    public function clear(): void
    {
        $this->repositoryRegion->truncate();
    }

    /**
     * Заполнить таблицу регионов
     */
    public function fill(): void
    {
        $entities = $this->getEntities($this->resourceRegion, $this->mapperRegion);
        $this->repositoryRegion->insert($entities);
    }

    /**
     * Получить все записи
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->repositoryRegion->findAll();
    }
}