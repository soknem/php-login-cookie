<?php
require 'sakila_db.php';

header('Content-Type: application/json');

if (isset($_GET['store_id'])) {
    $store_id = $_GET['store_id'];
    $stmt = $sakila_pdo->prepare("
        SELECT c.customer_id, c.first_name, c.last_name, c.email, c.active, c.create_date, c.last_update,
               a.address, ci.city, co.country, ci_store.city AS store_city
        FROM customer c
        JOIN address a ON c.address_id = a.address_id
        JOIN city ci ON a.city_id = ci.city_id
        JOIN country co ON ci.country_id = co.country_id
        JOIN store s ON c.store_id = s.store_id
        JOIN address a_store ON s.address_id = a_store.address_id
        JOIN city ci_store ON a_store.city_id = ci_store.city_id
        WHERE s.store_id = ?
        LIMIT 1
    ");
    $stmt->execute([$store_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($customer) {
        echo json_encode($customer);
    } else {
        echo json_encode(['error' => 'Customer not found']);
    }
} else {
    echo json_encode(['error' => 'No store_id provided']);
}
?>