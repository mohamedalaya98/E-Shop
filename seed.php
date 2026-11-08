<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();

    // Insertion des catégories
    $pdo->exec("INSERT IGNORE INTO categories (id, name, slug) VALUES 
        (1, 'Électronique', 'electronique'),
        (2, 'Vêtements', 'vetements'),
        (3, 'Accessoires', 'accessoires')");

    // Insertion des produits
    $pdo->exec("INSERT IGNORE INTO products (category_id, title, slug, description, price, stock, image) VALUES 
        (1, 'Écouteurs Sans Fil Pro', 'ecouteurs-sans-fil-pro', 'Son haute fidélité, réduction active du bruit et autonomie de 24h.', 79.99, 15, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500'),
        (1, 'Montre Connectée Sport', 'montre-connectee-sport', 'Suivi d’activité, GPS intégré, étanche et design élégant.', 149.99, 8, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500'),
        (2, 'T-Shirt Premium Coton', 't-shirt-premium-coton', '100% coton bio, coupe moderne et confortable.', 29.99, 50, 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500'),
        (3, 'Sac à Dos Urbain', 'sac-a-dos-urbain', 'Compartiment ordinateur renforcé, design minimaliste et imperméable.', 59.99, 20, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500')");

    echo "<div style='font-family:sans-serif; padding:20px; background:#d4edda; color:#155724; border-radius:5px;'>Données de test insérées avec succès ! <a href='index.php'>Retourner à l'accueil</a></div>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}