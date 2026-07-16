<?php

class Cart
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getOrCreateForUser(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM carts WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        if ($row) return (int)$row['id'];

        $stmt = $this->pdo->prepare('INSERT INTO carts (user_id) VALUES (:uid)');
        $stmt->execute(['uid' => $userId]);
        return (int)$this->pdo->lastInsertId();
    }

    public function items(int $cartId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ci.id AS cart_item_id, ci.quantity, p.id AS product_id, p.name, p.price, p.unit,
                    p.image_url, p.stock_quantity, fp.farm_name, u.full_name AS farmer_name
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             JOIN farmer_profiles fp ON fp.id = p.farmer_id
             JOIN users u ON u.id = fp.user_id
             WHERE ci.cart_id = :cid
             ORDER BY ci.created_at ASC'
        );
        $stmt->execute(['cid' => $cartId]);
        return $stmt->fetchAll();
    }

    public function addItem(int $cartId, int $productId, int $quantity): void
    {
        $stmt = $this->pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid');
        $stmt->execute(['cid' => $cartId, 'pid' => $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $this->pdo->prepare('UPDATE cart_items SET quantity = :qty WHERE id = :id');
            $update->execute(['qty' => $newQty, 'id' => $existing['id']]);
        } else {
            $insert = $this->pdo->prepare(
                'INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cid, :pid, :qty)'
            );
            $insert->execute(['cid' => $cartId, 'pid' => $productId, 'qty' => $quantity]);
        }
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cartItemId);
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE cart_items SET quantity = :qty WHERE id = :id');
        $stmt->execute(['qty' => $quantity, 'id' => $cartItemId]);
    }

    public function removeItem(int $cartItemId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE id = :id');
        $stmt->execute(['id' => $cartItemId]);
    }

    public function clear(int $cartId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cid');
        $stmt->execute(['cid' => $cartId]);
    }

    public function total(int $cartId): float
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(ci.quantity * p.price), 0) AS total
             FROM cart_items ci JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = :cid'
        );
        $stmt->execute(['cid' => $cartId]);
        return (float)$stmt->fetch()['total'];
    }
}
