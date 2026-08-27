-- ============================================
-- banco: avaliacao-titan-php
-- script de criação das tabelas
-- ============================================
CREATE TABLE
    user (
        id_user BIGINT (20) NOT NULL AUTO_INCREMENT,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        update_at DATETIME DEFAULT NULL,
        ativo TINYINT (1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id_user),
        UNIQUE KEY uq_user_email (email)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    service (
        id_service BIGINT (20) NOT NULL AUTO_INCREMENT,
        description VARCHAR(45) NOT NULL,
        price DECIMAL(11, 3) NOT NULL,
        created_at DATETIME NOT NULL,
        update_at DATETIME DEFAULT NULL,
        finished_at DATETIME DEFAULT NULL,
        commission_user DECIMAL(11, 3) DEFAULT NULL,
        user_id_user BIGINT (20) NOT NULL,
        PRIMARY KEY (id_service),
        KEY fk_service_user (user_id_user),
        CONSTRAINT fk_service_user FOREIGN KEY (user_id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ============================================
-- usuário de teste para login
-- senha: 123456 (hash gerado com password_hash do PHP)
-- ============================================
INSERT INTO
    user (name, email, password, created_at, ativo)
VALUES
    (
        'José Silva',
        'josesilva@jminformatica.com',
        '$2y$10$ZZo7S5jOMJl4CbpM7o/qhey/r5X5B7FSchSAyHvY7Epr2QGKKLtZ6',
        NOW (),
        1
    );