<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

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
     * @return bool
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients 
            (name, email, phone, address, favorites, abandoned_cart)
            VALUES
            (:name, :email, :phone, :address, :favorites, :abandoned_cart)'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'favorites' => $data['favorites'] ?: null,
            'abandoned_cart' => $data['abandoned_cart'] ?: null,
        ]);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
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
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
            'favorites' => $data['favorites'] ?: null,
            'abandoned_cart' => $data['abandoned_cart'] ?: null,
        ]);
    }
}