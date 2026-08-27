<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * model responsavel por toda a comunicacao com a tabela `user`.
 */
class User
{
    /**
     * busca um usuario ativo pelo email.
     * retorna o array associativo com os dados, ou null se não encontrar.
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email AND ativo = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * busca um usuario pelo id. usado para pegar os dados do usuário logado.
     */
    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT * FROM user WHERE id_user = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();

        return $user ?: null;
    }
}
