<?php

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function baseSelect(): string
    {
        return "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       fp.farm_name, fp.district, u.full_name AS farmer_name
                FROM products p
                JOIN categories c ON c.id = p.category_id
                JOIN farmer_profiles fp ON fp.id = p.farmer_id
                JOIN users u ON u.id = fp.user_id";
    }

    public function search(array $filters): array
    {
        $sql = $this->baseSelect() . ' WHERE p.is_active = 1';
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= ' AND p.name LIKE :q';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category'])) {
            $sql .= ' AND c.slug = :category';
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['district'])) {
            $sql .= ' AND fp.district = :district';
            $params['district'] = $filters['district'];
        }

        $sql .= match ($filters['sort'] ?? '') {
            'price_asc'  => ' ORDER BY p.price ASC',
            'price_desc' => ' ORDER BY p.price DESC',
            default      => ' ORDER BY p.created_at DESC',
        };

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function featured(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare($this->baseSelect() . ' WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare($this->baseSelect() . ' WHERE p.slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare($this->baseSelect() . ' WHERE p.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        $stmt = $this->pdo->prepare(
            $this->baseSelect() . ' WHERE p.category_id = :cat AND p.id != :id AND p.is_active = 1 LIMIT :lim'
        );
        $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function byFarmer(int $farmerId): array
    {
        $stmt = $this->pdo->prepare(
            $this->baseSelect() . ' WHERE p.farmer_id = :fid ORDER BY p.created_at DESC'
        );
        $stmt->execute(['fid' => $farmerId]);
        return $stmt->fetchAll();
    }

    public function findByIdForFarmer(int $id, int $farmerId): ?array
    {
        $stmt = $this->pdo->prepare($this->baseSelect() . ' WHERE p.id = :id AND p.farmer_id = :fid LIMIT 1');
        $stmt->execute(['id' => $id, 'fid' => $farmerId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $slug = slugify($data['name']);
        if ($this->findBySlug($slug)) {
            $slug .= '-' . substr(md5((string)microtime()), 0, 5);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO products (farmer_id, category_id, name, slug, description, price, unit, stock_quantity, image_url)
             VALUES (:farmer_id, :category_id, :name, :slug, :description, :price, :unit, :stock_quantity, :image_url)'
        );
        $stmt->execute([
            'farmer_id'      => $data['farmer_id'],
            'category_id'    => $data['category_id'],
            'name'           => $data['name'],
            'slug'           => $slug,
            'description'    => $data['description'] ?? null,
            'price'          => $data['price'],
            'unit'           => $data['unit'] ?? 'kg',
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'image_url'      => $data['image_url'] ?? '/assets/images/product-placeholder.svg',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, int $farmerId, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET name = :name, category_id = :category_id, description = :description,
                price = :price, unit = :unit, stock_quantity = :stock_quantity,
                image_url = :image_url, is_active = :is_active
             WHERE id = :id AND farmer_id = :farmer_id'
        );
        return $stmt->execute([
            'name'           => $data['name'],
            'category_id'    => $data['category_id'],
            'description'    => $data['description'] ?? null,
            'price'          => $data['price'],
            'unit'           => $data['unit'] ?? 'kg',
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'image_url'      => $data['image_url'] ?? '/assets/images/product-placeholder.svg',
            'is_active'      => $data['is_active'] ?? 1,
            'id'             => $id,
            'farmer_id'      => $farmerId,
        ]);
    }

    public function delete(int $id, int $farmerId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id AND farmer_id = :farmer_id');
        return $stmt->execute(['id' => $id, 'farmer_id' => $farmerId]);
    }

    public function decrementStock(int $id, int $qty): void
    {
        $stmt = $this->pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id');
        $stmt->execute(['qty' => $qty, 'id' => $id]);
    }

    public function count(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) AS c FROM products')->fetch()['c'];
    }
}
