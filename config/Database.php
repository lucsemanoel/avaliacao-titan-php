<?php

/**
 * classe responsavel por criar e fornecer a conexao PDO com o banco de dados.
 * usa o padrao Singleton para evitar abrir multiplas conexoes desnecessarias.
 */
class Database
{
    private static ?PDO $instance = null;

    // ajuste esses dados conforme o seu ambiente local (XAMPP)
    private const HOST = '127.0.0.1';
    private const DB_NAME = 'avaliacao-titan-php';
    private const USER = 'root';
    private const PASSWORD = '';
    private const CHARSET = 'utf8mb4';

    // impede que a classe seja instanciada diretamente (ex: new Database())
    private function __construct() {}

    /**
     * retorna a instancia unica (singleton) da conexao PDO.
     * se ainda nao existir, cria a conexao; se ja existir, reaproveita.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=' . self::CHARSET;

            try {
                self::$instance = new PDO($dsn, self::USER, self::PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // em producao isso deveria ir para um log, nao para a tela
                die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
