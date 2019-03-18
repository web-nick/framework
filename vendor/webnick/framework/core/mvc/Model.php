<?php

namespace webnick\framework\core\mvc;

use webnick\framework\core\Config;
use webnick\framework\helpers\Pagination;

/**
 * Class Model базовый класс модели
 *
 * @package framework\core\mvc
 */
abstract class Model
{
    /* @var $db \PDO */
    protected static $db = null;

    /* @var $pagination array */
    protected $pagination = null;

    abstract function tableName(): string;

    public function __construct()
    {
        self::$db or $this->setDb();
    }

    /**
     * Установить подключение к базе данных
     */
    protected function setDb(): void
    {
        if (!self::$db) {
            $config = Config::getConfig('db');

            self::$db = new \PDO($config['dsn'], $config['user'], $config['password'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

            self::$db->exec('SET NAMES UTF8');
        }
    }

    /**
     * Получить объект для работы с базой данных
     *
     * @return \PDO
     */
    public function getDb(): \PDO
    {
        return self::$db;
    }

    /**
     * Простой метод сохранения (добавление/обновление) записи в БД
     *
     * @param array $columns поля вида "имя столбца => значение столбца"
     * @param string|null $where условия WHERE написанное вручную
     * @return int
     */
    public function save(array $columns, string $where = null): int
    {
        $set = $this->createSetQuery($columns);

        $query = ($where ? "UPDATE {$this->tableName()} SET" : "INSERT INTO {$this->tableName()} SET") . $set;

        $where and $query .= "WHERE $where";

        return self::$db->exec($query);
    }

    /**
     * Вернуть записи из БД
     *
     * @param array $params
     * @return array|null
     */
    public function find(array $params = []): ?array
    {
        $params = array_merge([
            'select' => '*',
            'join' => null,
            'where' => null,
            'order' => null,
            'limit' => null,
            'alias' => 't1',
            'one' => null,
            'usePagination' => null,
        ], $params);

        $queries = $this->createQueries($params);

        if ($params['usePagination']) {
            $this->pagination = Pagination::getPagination(self::$db->query($queries['count'])->fetchColumn());

            $queries['query'] .= $this->pagination['sqlLimit'];
        }

        $method = $params['one'] ? 'fetch' : 'fetchAll';

        return ($result = self::$db->query($queries['query'])->$method(\PDO::FETCH_ASSOC)) ? $result : null;
    }

    /**
     * Метод для множественного INSERT-а
     *
     * @param array $data
     * @return int
     */
    public function multipleInsert(array $data): int
    {
        $rows = '(' . implode(', ', array_keys(current($data))) . ')';

        $values = '';

        foreach ($data as $datum)
            $values .= '(' . implode(', ', $datum) . '),';

        $values = rtrim($values, ',');

        $query = "INSERT INTO {$this->tableName()} $rows VALUES $values";

        return self::$db->exec($query);
    }

    /**
     * Обвертка для MySQL-выражения EXISTS.
     * Проверка на наличие записи в БД
     *
     * @param string $where
     * @return bool
     */
    public function exists(string $where): bool
    {
        $query = "SELECT EXISTS (SELECT 1 FROM {$this->tableName()} WHERE $where LIMIT 1)";

        return self::$db->query($query)->fetchColumn() ? true : false;
    }

    /**
     * Вернуть массив с расчитанной пагинацией, которая должна быть установлена ранее каким-либо методом
     *
     * @return array
     */
    public function getPagination(): array
    {
        return $this->pagination;
    }

    /**
     * Начать транзакцию
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return self::$db->beginTransaction();
    }

    /**
     * Зафиксировать транзакцию
     *
     * @return bool
     */
    public function commitTransaction(): bool
    {
        return self::$db->commit();
    }

    /**
     * Откат транзакции
     *
     * @return bool
     */
    public function rollBackTransaction(): bool
    {
        return self::$db->rollBack();
    }

    /**
     * Выполнить переданный MySQL-запрос
     *
     * @param string $query
     * @return int
     */
    public function query(string $query): int
    {
        return self::$db->exec($query);
    }

    /**
     * Вернуть ID последней вставленной строки
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return self::$db->lastInsertId();
    }

    /**
     * Сформировать SET часть MySQL-запроса
     *
     * @param array $columns
     * @return string
     */
    protected function createSetQuery(array $columns): string
    {
        $set = '';

        foreach ($columns as $col_name => $value) {
            if (is_null($value))
                $set .= " `$col_name` IS NULL, ";
            elseif ($value === false)
                continue;
            else
                $set .= " `$col_name` = '$value', ";
        }

        return rtrim($set, ', ');
    }

    /**
     * Сформировать запросы на выборку и COUNT()
     *
     * @param array $params
     * @return array
     */
    protected function createQueries(array $params): array
    {
        $where = $params['where'] ? "WHERE {$params['where']}" : '';

        $order = $params['order'] ? "ORDER BY {$params['order']}" : '';

        $limit = '';

        if ($params['one'])
            $limit = 'LIMIT 1';
        elseif (!$params['usePagination'] and $params['limit'])
            $limit = "LIMIT {$params['limit']}";

        $from = "FROM {$this->tableName()} AS {$params['alias']}";

        $result['query'] = "SELECT {$params['select']} $from {$params['join']} $where $order $limit";

        $result['count'] = "SELECT COUNT(*) $from {$params['join']} $where";

        return $result;
    }


}