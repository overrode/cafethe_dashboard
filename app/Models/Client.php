<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

/**
 * Represents a client entity and provides methods
 */
class Client
{
    private PDO $db;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM clients ORDER BY id DESC'
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
            'SELECT * FROM clients WHERE id = :id LIMIT 1'
        );

        $stmt->execute(['id' => $id]);

        $client = $stmt->fetch();

        return $client ?: null;
    }

    /**
     * @param array $data
     * @return int
     * @throws Throwable
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients 
            (name, email, phone, address, favorites, abandoned_cart, password, source, is_active)
            VALUES
            (:name, :email, :phone, :address, :favorites, :abandoned_cart, :password, :source, :is_active)'
        );

        // Prepare client data.
        $password = !empty($data['password'])
            ? password_hash($data['password'], PASSWORD_DEFAULT)
            : null;

        $source = $data['source'] ?? 'dashboard';
        $isActive = (int) ($data['is_active'] ?? 1);

        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'favorites' => $data['favorites'] ?? null,
            'abandoned_cart' => $data['abandoned_cart'] ?? null,
            'password' => $password,
            'source' => $source,
            'is_active' => $isActive,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE clients
             SET name = :name,
                 email = :email,
                 phone = :phone,
                 address = :address,
                 favorites = :favorites,
                 abandoned_cart = :abandoned_cart
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'favorites' => $data['favorites'] ?? null,
            'abandoned_cart' => $data['abandoned_cart'] ?? null,
        ]);
    }

    /**
     * @param string $email
     * @return array|null
     * @throws Throwable
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM clients WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            'email' => $email,
        ]);

        $client = $stmt->fetch();

        return $client ?: null;
    }

    /*
     * @param int $id
     * @param string $password
     * @return bool/
     * @throws Throwable
     */
    public function updatePassword(int $id, string $password): bool
    {
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->db->prepare(
            'UPDATE clients
             SET password = :password
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'password' => $passwordHash,
        ]);
    }

    /*
     * Activate or deactivate a client.
     *
     * @param int $id
     * @param bool $isActive
     * @return bool
     * @throws Throwable
     */
    public function setActive(int $id, bool $isActive): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE clients
             SET is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'is_active' => $isActive ? 1 : 0,
        ]);
    }
}