## 1. Требования к ПО

Стек:

- PHP 8.0
- MySQL 8.0
- Symfony 5.2

## 2. Процесс установки и настройки

### 2.1 Заполнение таблиц region, city и warehouse

Заполенение осуществляется с помощью двух команд. Код и все ресурсы, с ними связанные, находятся в директории /bundles/LocationImport/, в пространстве имен LocationImport

##### 2.1.1 Заполнение таблиц region и city

Осуществляется с помощью команды 

`fill:locations`

Команда выполняет:

1. Парсинг csv-файлов region.csv и city.csv. Файлы хранятся в директории /bundles/LocationImport/Resources/files/, 
пути к файлам заданы в конфиге /bundles/LocationImport/Resources/config/services.yaml
2. Очистку таблиц region и city
3. Заполнение таблицы новыми данными

##### 2.1.2 Заполнение таблицы warehouse

Осуществляется с помощью команды 

`fill:stores`

Запускать команду только после заполенения таблиц region и city

Команда выполняет

1. Очистку таблицы warehouse
2. Генерацию складов и последующее сохранение их в таблицу warehouse

## 3. REST API

#### 3.1 API для работы с остатками

##### 3.1.1 Метод возвращает все остатки по товару с id=productId

`/api/v1/remains/{productId}`

Где

- productId - артикул товара

##### 3.1.2 Метод возвращает все остатки товара на конкретном складе

`/api/v1/remains/{productId}/warehouse/{warehouseId}`

Где

- productId - артикул товара
- warehouseId - код склада (напр. С000)

##### 3.1.3 Метод возвращает все остатки товара в конкретном городе

`/api/v1/remains/{productId}/city/{cityId}`

Где

- productId - артикул товара
- cityId - id города из таблицы city

##### 3.1.4 Метод возвращает все остатки товара в конкретном регионе

`/api/v1/remains/{productId}/region/{regionId}`

Где

- productId - артикул товара
- regionId - id региона из таблицы region

##### 3.1.5 Форматы ответов

Формат успешного ответа

```
{
   "success":"true",
   "data":{
      "stores":{
         "C000":[
            {
               "product_id":"1000-0000",
               "quantity":3
            },
            {
               "product_id":"1002-0000",
               "quantity":3
            }
         ]
      }
   }
}
```

Формат ответа с ошибкой

```
{
   "success":"false",
   "error":"Текст ошибки"
}
```

#### 3.2 API для работы со статистикой

##### 3.2.1 Метод возвращает последнюю запись из таблицы статистики

`/api/v1/import-stat`

##### 3.2.2 Метод возвращает запись из таблицы статистики c конкретным id

`/api/v1/import-stat/{id}`

Где

- id - id записи из таблицы import_stat

##### 3.2.3 Форматы ответов

Формат успешного ответа

```
{
   "success":"true",
   "data":{
      "id":2,
      "errorsCount":0,
      "totalCount":1,
      "loadAverage":"1.0",
      "isFull":1,
      "rowsDeleted":1,
      "timeStart":"2021-12-12 00:00:00",
      "timeEnd":"2021-12-12 00:10:00",
   }
}
```

Формат ответа с ошибкой

```
{
   "success":"false",
   "error":"Текст ошибки"
}
```

## 4. Использование импорта

#### Принцип работы импорта:

Родительская команда запускает в несколько процессов дочерние команды для обработки файлов с остатками.

### 4.1 Родительская команда для импорта остатков

Команда выполняет:

1. Поиск и распаковку gz файлов с остатками в временную папку
2. Инициализирует статистику
3. Порождает процессы обработки распакованного файла остатков
4. Удаляет старые записи в случае полной выгрузки

Использование

```
import:run [опции]
```

Основные опции:

```
-p, --path[=PATH]
    Путь к директории с архивами gz файлов импорта. Строка.
    [по-умолчанию: "/var/www/test-task/import"]
    
-t, --type[=TYPE]
    Тип импорта (full, partial). Строка
    [по-умолчанию: "full"]

-mt, --max-threads[=MAX-THREADS]
    Максимальное колчество процессов для обработки файлов. Целое положительное число.
    [по-умолчанию: 20]
    
-tp, --threads-pause[=THREADS-PAUSE]
    Пауза (мс) если все процессы заняты. Целое положительное число.
    [по-умолчанию: 5]
    
-cs, --db-clean-step-count[=DB-CLEAN-STEP-COUNT]
    Количество удаляемых за итерацию строк из БД после полного импорта. Целое положительное число. 
    [по-умолчанию: 1000]
    
-cp, --db-clean-pause[=DB-CLEAN-PAUSE]
    Пауза между удалениями из БД (мс). Целое положительное число.
    [по-умолчанию: 1]
```

### 4.2 Дочерняя команда обработки файла с остатками

Команда выполняет:

1. Проверяет существование склада
2. Обрабатывает построчно файл
3. Добавляет/изменяет остатки
4. Фиксирует статистику изменений

Использование

```
import:process-file [опции]
```

Основные опции:

```
-p, --path[=PATH]
    Путь к файлу для обработки. Строка.
    
-i, --import-id=IMPORT-ID
    Идентификатор процесса статистики. Целое положительное число.
```