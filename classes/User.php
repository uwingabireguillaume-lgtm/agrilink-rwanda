<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (full_name, email, password, phone, address, role)
             VALUES (:full_name, :email, :password, :phone, :address, :role)'
        );
        $stmt->execute([
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_BCRYPT),
            'phone'     => $data['phone'],
            'address'   => $data['address'] ?? null,
            'role'      => $data['role'] ?? 'consumer',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS c FROM users WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return (int)$stmt->fetch()['c'];
    }
}
