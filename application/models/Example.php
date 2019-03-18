<?php

namespace models;

use webnick\framework\core\mvc\Model;

/**
 * Class Example пример модели
 *
 * @package models
 */
class Example extends Model
{

    public function tableName(): string
    {
        return 'example';
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getProduct(int $id): ?array
    {
        return $this->find([
            'select' => 'c.*',
            'join' => "INNER JOIN someTbl u ON u.id = c.user_id",
            'where' => "id = $id",
            'alias' => 'c',
            'one' => true,
        ]);
    }
}