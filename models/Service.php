<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * model responsavel por toda a comunicacao com a tabela `service`.
 *
 * regra de status: um servico eh considerado "Finalizado" quando possui
 * finished_at preenchido; caso contrario eh considerado "Pendente".
 */
class Service
{
    /**
     * lista os servicos prestados pelos funcionarios, com filtros opcionais.
     * traz o nome do usuario responsavel via JOIN.
     *
     * filtros aceitos (todos opcionais):
     *  - date_start, date_end: filtra por created_at dentro do periodo
     *  - description: filtra por nome/descricao do servico (LIKE)
     *  - status: 'pendente' ou 'finalizado'
     *  - user_name: filtra pelo nome do usuario que prestou o servico (LIKE)
     */
    public static function findAll(array $filters = []): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT s.id_service, s.description, s.price, s.created_at,
                       s.finished_at, s.commission_user, u.name AS user_name
                FROM service s
                INNER JOIN user u ON u.id_user = s.user_id_user
                WHERE 1 = 1';

        $params = [];

        if (!empty($filters['date_start'])) {
            $sql .= ' AND s.created_at >= :date_start';
            $params['date_start'] = $filters['date_start'] . ' 00:00:00';
        }

        if (!empty($filters['date_end'])) {
            $sql .= ' AND s.created_at <= :date_end';
            $params['date_end'] = $filters['date_end'] . ' 23:59:59';
        }

        if (!empty($filters['description'])) {
            $sql .= ' AND s.description LIKE :description';
            $params['description'] = '%' . $filters['description'] . '%';
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'pendente') {
                $sql .= ' AND s.finished_at IS NULL';
            } elseif ($filters['status'] === 'finalizado') {
                $sql .= ' AND s.finished_at IS NOT NULL';
            }
        }

        if (!empty($filters['user_name'])) {
            $sql .= ' AND u.name LIKE :user_name';
            $params['user_name'] = '%' . $filters['user_name'] . '%';
        }

        $sql .= ' ORDER BY s.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * soma o valor de todos os servicos prestados pelo usuario informado
     * (independente do status), usada no card de "Valor Total" do dashboard.
     */
    public static function totalValueByUser(int $userId): float
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT COALESCE(SUM(price), 0) AS total
                                FROM service
                                WHERE user_id_user = :user_id');
        $stmt->execute(['user_id' => $userId]);

        return (float) $stmt->fetchColumn();
    }

    /**
     * busca os ultimos servicos pendentes (sem finished_at) do usuario,
     * usada no card de "Servicos Pendentes" do dashboard.
     */
    public static function pendingByUser(int $userId, int $limit = 5): array
    {
        $pdo = Database::getConnection();

        // LIMIT nao aceita bind como parametro tipado em algumas versoes,
        // entao validamos/forcamos int antes de concatenar com seguranca.
        $limit = max(1, $limit);

        $stmt = $pdo->prepare("SELECT id_service, description, price, created_at
                                FROM service
                                WHERE user_id_user = :user_id
                                  AND finished_at IS NULL
                                ORDER BY created_at DESC
                                LIMIT {$limit}");
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
