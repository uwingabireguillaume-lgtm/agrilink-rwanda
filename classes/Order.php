<?php

class Order
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Places an order from the given cart items, splitting line items per farmer
     * so each vendor can manage fulfillment of their own products independently.
     *
     * @throws Exception on insufficient stock or DB error (transaction rolled back)
     */
    public function placeOrder(int $userId, array $cartItems, array $customer): array
    {
        if (empty($cartItems)) {
            throw new Exception('Your cart is empty.');
        }

        foreach ($cartItems as $item) {
            if ((int)$item['quantity'] > (int)$item['stock_quantity']) {
                throw new Exception('Not enough stock for ' . $item['name'] . '.');
            }
        }

        $totalAmount = array_reduce($cartItems, function ($sum, $item) {
            return $sum + ($item['quantity'] * $item['price']);
        }, 0.0);

        $this->pdo->beginTransaction();
        try {
            $orderNumber = generateOrderNumber();

            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone,
                    shipping_address, total_amount, payment_method)
                 VALUES (:order_number, :user_id, :customer_name, :customer_email, :customer_phone,
                    :shipping_address, :total_amount, :payment_method)'
            );
            $stmt->execute([
                'order_number'     => $orderNumber,
                'user_id'          => $userId,
                'customer_name'    => $customer['customer_name'],
                'customer_email'   => $customer['customer_email'],
                'customer_phone'   => $customer['customer_phone'],
                'shipping_address' => $customer['shipping_address'],
                'total_amount'     => $totalAmount,
                'payment_method'   => $customer['payment_method'] ?? 'cash_on_delivery',
            ]);
            $orderId = (int)$this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, farmer_id, product_name, quantity, unit_price, subtotal)
                 VALUES (:order_id, :product_id, :farmer_id, :product_name, :quantity, :unit_price, :subtotal)'
            );
            $stockStmt = $this->pdo->prepare(
                'UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id'
            );

            // Need farmer_id per product; look it up.
            $farmerLookup = $this->pdo->prepare('SELECT farmer_id FROM products WHERE id = :id');

            foreach ($cartItems as $item) {
                $farmerLookup->execute(['id' => $item['product_id']]);
                $farmerId = $farmerLookup->fetch()['farmer_id'];

                $itemStmt->execute([
                    'order_id'     => $orderId,
                    'product_id'   => $item['product_id'],
                    'farmer_id'    => $farmerId,
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['price'],
                    'subtotal'     => $item['quantity'] * $item['price'],
                ]);

                $stockStmt->execute(['qty' => $item['quantity'], 'id' => $item['product_id']]);
            }

            $this->pdo->commit();

            return $this->findByIdWithItems($orderId);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findByIdWithItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();

        $itemsStmt = $this->pdo->prepare(
            'SELECT oi.*, fp.farm_name, u.full_name AS farmer_name
             FROM order_items oi
             JOIN farmer_profiles fp ON fp.id = oi.farmer_id
             JOIN users u ON u.id = fp.user_id
             WHERE oi.order_id = :oid'
        );
        $itemsStmt->execute(['oid' => $orderId]);
        $order['items'] = $itemsStmt->fetchAll();

        return $order;
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC');
        $stmt->execute(['uid' => $userId]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $order['items'] = $this->itemsForOrder((int)$order['id']);
        }
        return $orders;
    }

    public function itemsForOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT oi.*, fp.farm_name FROM order_items oi
             JOIN farmer_profiles fp ON fp.id = oi.farmer_id
             WHERE oi.order_id = :oid'
        );
        $stmt->execute(['oid' => $orderId]);
        return $stmt->fetchAll();
    }

    public function itemsForFarmer(int $farmerId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT oi.*, o.order_number, o.created_at, o.customer_name, o.customer_phone, o.shipping_address, o.status AS order_status
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE oi.farmer_id = :fid
             ORDER BY o.created_at DESC'
        );
        $stmt->execute(['fid' => $farmerId]);
        return $stmt->fetchAll();
    }

    public function updateItemStatus(int $orderItemId, int $farmerId, string $status): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE order_items SET farmer_status = :status WHERE id = :id AND farmer_id = :fid'
        );
        return $stmt->execute(['status' => $status, 'id' => $orderItemId, 'fid' => $farmerId]);
    }

    public function count(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(*) AS c FROM orders')->fetch()['c'];
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders ORDER BY created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
