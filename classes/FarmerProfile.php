<?php

class FarmerProfile
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $userId, array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO farmer_profiles (user_id, farm_name, district, sector, bio, is_approved)
             VALUES (:user_id, :farm_name, :district, :sector, :bio, 1)'
        );
        $stmt->execute([
            'user_id'   => $userId,
            'farm_name' => $data['farm_name'],
            'district'  => $data['district'],
            'sector'    => $data['sector'] ?? null,
            'bio'       => $data['bio'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM farmer_profiles WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT fp.*, u.full_name, u.email, u.phone
             FROM farmer_profiles fp
             JOIN users u ON u.id = fp.user_id
             WHERE fp.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT fp.*, u.full_name, u.email, u.phone
             FROM farmer_profiles fp
             JOIN users u ON u.id = fp.user_id
             ORDER BY fp.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function toggleApproval(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE farmer_profiles SET is_approved = NOT is_approved WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) AS c FROM farmer_profiles')->fetch()['c'];
    }

    public function distinctDistricts(): array
    {
        $stmt = $this->pdo->query('SELECT DISTINCT district FROM farmer_profiles ORDER BY district ASC');
        return array_column($stmt->fetchAll(), 'district');
    }
}
