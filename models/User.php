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

    /**
     * verifica se ja existe um usuario ativo cadastrado com o e-mail
     * informado. usada pelo controller para validar antes de tentar
     * inserir (a coluna email tambem tem UNIQUE KEY no banco como
     * segunda camada de protecao).
     */
    public static function emailExists(string $email): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id_user FROM user WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * cadastra um novo usuario, salvando a senha ja com hash
     * (password_hash, mesmo algoritmo usado no login/password_verify).
     *
     * retorna o id do usuario recem-criado.
     */
    public static function create(string $name, string $email, string $password): int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('INSERT INTO user (name, email, password, created_at, ativo)
                                VALUES (:name, :email, :password, NOW(), 1)');

        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $pdo->lastInsertId();
    }
}
