<?php

namespace webnick\framework\core;

/**
 * Class User класс авторизации пользователя
 *
 * @package webnick\framework\core
 */
class User
{
    protected static $session_options = [
        'use_strict_mode' => false,
    ];

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (!self::sessionStart())
            throw new \Exception('Невозможно запустить сессию');
    }

    /**
     * Авторизировать пользователя
     *
     * @param array $identity
     * @throws \Exception
     */
    public function login(array $identity): void
    {
        $this->setIdentity($identity);
    }

    /**
     * Завершить сессию
     *
     * @return bool
     */
    public function logout(): bool
    {
        setcookie(session_name(), null, -1, session_get_cookie_params()['path']);

        return session_destroy();
    }

    /**
     * Получить идентифицирующую информацию о пользователе
     *
     * @return array|null
     */
    public function getIdentity(): ?array
    {
        return $_SESSION['identity'] ?? null;
    }

    /**
     * Установить идентифицирующую информацию о пользователе
     *
     * @param array $identity
     */
    protected function setIdentity(array $identity): void
    {
        $_SESSION['identity'] = $identity;
    }

    /**
     * Залигонен ли пользователь
     *
     * @return bool
     * @throws \Exception
     */
    public function isQuest(): bool
    {
        return isset($_SESSION['identity']) ? false : true;
    }

    /**
     * Начать сессию, если она еще не запущена
     *
     * @return bool
     * @throws \Exception
     */
    public static function sessionStart(): bool
    {
        $status = session_status();

        if ($status == PHP_SESSION_DISABLED)
            throw new \Exception('Session is disabled on this server');

        if ($status == PHP_SESSION_NONE)
            return self::createSession();

        return true;
    }

    /**
     * Создать директории сессионных файлов и запустить сессию
     *
     * @return bool
     * @throws \Exception
     */
    protected static function createSession(): bool
    {
        $config = Config::getConfig('user')['session'];

        $options = array_merge(self::$session_options, $config['params']);

        $config['disable_gc'] and self::disableGC();

        $options['save_path'] = str_replace('\\', '/', $options['save_path']);

        if (isset($_COOKIE[session_name()]))
            self::createSessionSubFolders($options['save_path'], $_COOKIE[session_name()]);
        else {
            if ($options['use_strict_mode'])
                throw new \Exception('Невозможно установить session_id() при включенном strict_mode');

            /** @noinspection PhpStatementHasEmptyBodyInspection Проверка на коллизию */
            while (file_exists(self::getSessionFolder($options['save_path'], $id = session_create_id()) . "/sess_$id")) ;

            session_id($id);

            self::createSessionSubFolders($options['save_path'], $id);
        }

        return session_start($options);
    }

    /**
     * Отключение сборщика мусора сессий PHP
     */
    protected static function disableGC(): void
    {
        ini_set('session.gc_probability', 0);
    }

    /**
     * Создание поддиректорий для сессий
     *
     * @param string $save_path
     * @param string $session_id
     * @return bool|null
     */
    protected static function createSessionSubFolders(string $save_path, string $session_id): ?bool
    {
        if (count($path = explode(';', $save_path)) < 2 or ini_get('session.save_handler') != 'files')
            return null;

        if (!$dir = self::getSessionFolder($save_path, $session_id))
            return null;

        is_dir($dir) or $result = mkdir($dir, 0755, true);

        return $result ?? null;
    }

    protected static function getSessionFolder(string $save_path, string $session_id): ?string
    {
        if (ini_get('session.save_handler') != 'files')
            return null;

        if (count($path = explode(';', $save_path)) == 1)
            return $save_path;

        $dir = rtrim(array_pop($path), '/');

        for ($i = 0; $i < $path[0]; $i++)
            $dir .= '/' . $session_id[$i];

        return $dir;
    }


}