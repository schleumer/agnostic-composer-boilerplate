<?php

/**
 * Singleton responsável por iniciar e armazenar objetos da aplicação
 */
class App
{
    /**
     * @var \League\Plates\Engine
     */
    public static $templates;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    public static $request;

    /**
     * @var \Whoops\Run
     */
    public static $whoops;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public static $response;

    public static function boot()
    {
        self::$whoops = new \Whoops\Run;
        self::$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        self::$whoops->register();

        self::$request = \Request::capture();

        self::$response = \Symfony\Component\HttpFoundation\Response::create();

        self::$templates = new \League\Plates\Engine(__DIR__ . '/../templates');
    }
}