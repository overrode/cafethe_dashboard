<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * User Class
 */
class User
{
    private PDO $db;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1'
        );

        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, role, is_active FROM users ORDER BY id DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, role, is_active FROM users WHERE id = :id LIMIT 1'
        );

        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role, is_active)
             VALUES (:name, :email, :password, :role, :is_active)'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ]);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare(
                'UPDATE users
                 SET name = :name,
                     email = :email,
                     password = :password,
                     role = :role,
                     is_active = :is_active
                 WHERE id = :id'
            );

            return $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'role' => $data['role'],
                'is_active' => $data['is_active'],
            ]);
        }

        $stmt = $this->db->prepare(
            'UPDATE users
             SET name = :name,
                 email = :email,
                 role = :role,
                 is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET is_active = 0 WHERE id = :id'
        );

        return $stmt->execute(['id' => $id]);
    }
}