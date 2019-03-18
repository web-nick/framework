<?php

namespace webnick\framework\helpers;

use webnick\framework\core\Config;

/**
 * Class Pagination helper пагинации
 *
 * @package webnick\framework\helpers
 */
class Pagination
{
    /**
     * Получить данные пагинации
     *
     * @param int $total
     * @param int|null $limit
     * @return array
     */
    public static function getPagination(int $total, int $limit = null): array
    {
        $limit or $limit = Config::getConfig('pagination')['limit'];

        $getParam = Config::getConfig('pagination')['getParam'];

        $page = ($page = isset($_GET[$getParam]) ? (int)$_GET[$getParam] : 1) < 1 ? 1 : $page;

        $offset = ($page - 1) * $limit;

        return [
            'totalRows' => $total,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page,
            'offset' => $offset,
            'limit' => $limit,
            'sqlLimit' => "LIMIT $offset, $limit"
        ];
    }
}