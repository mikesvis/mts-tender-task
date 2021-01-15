<?php


namespace LocationImportBundle\Service;


use App\Repository\CityRepository;
use LocationImportBundle\Handler\HandlerFactory;
use LocationImportBundle\Handler\Mapper;

/**
 * Сервис для работы с данными городов
 */
class ServiceCity extends AbstractServiceLocation
{
    /** @var string */
    protected string $resourceCity;

    /** @var Mapper  */
    protected Mapper $mapperCity;

    /** @var CityRepository  */
    protected CityRepository $repositoryCity;

    /**
     * ServiceCity конструктор
     *
     * @param string $resourceCity Источних входных данных для городов
     * @param CityRepository $repositoryCity Репозиторий для работы с сущностями из базы
     * @param HandlerFactory $handlerFactory Фабрика обработчиков входных данных
     */
    public function __construct(
        string $resourceCity,
        CityRepository $repositoryCity,
        HandlerFactory $handlerFactory
    )
    {
        $this->resourceCity = $resourceCity;
        $this->repositoryCity = $repositoryCity;
        $this->parser = $handlerFactory->getParser();
        $this->mapperCity = $handlerFactory->getMapper();
    }

    /**
     * Очитить таблицу городов
     */
    public function clear(): void
    {
        $this->repositoryCity->truncate();
    }

    /**
     * Заполнить таблицу городов
     */
    public function fill(): void
    {
        $entities = $this->getEntities($this->resourceCity, $this->mapperCity);
        $this->repositoryCity->insert($entities);
    }
}