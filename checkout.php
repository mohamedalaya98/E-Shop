<?php
session_start();
require_once __DIR__ . '/config/database.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name) || empty($email) || empty($address)) {
        $error = 'Veuillez remplir tous les champs du formulaire.';
    } else {
        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            // 1. Vérifier ou insérer l'utilisateur rapidement
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $userId = $user['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
                $stmt->execute([$name, $email, password_hash('password123', PASSWORD_DEFAULT)]);
                $userId = $pdo->lastInsertId();
            }

            // 2. Créer la commande
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'paid')");
            $stmt->execute([$userId, $total]);
            $orderId = $pdo->lastInsertId();

            // 3. Insérer les articles de la commande
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $stmtItem->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();

            // Vider le panier et rediriger vers la confirmation
            $_SESSION['cart'] = [];
            $_SESSION['last_order_id'] = $orderId;
            header('Location: success.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erreur lors de la commande : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander - E-SHOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-black tracking-tight text-gray-900"><a href="index.php">E-SHOP<span class="text-orange-600">.</span></a></h1>
            <a href="cart.php" class="text-sm font-medium text-orange-600 hover:underline">← Retour au panier</a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Finaliser votre commande</h2>

        <?php if (!empty($error)): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg font-semibold">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Formulaire -->
            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form action="checkout.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Nom complet</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Adresse e-mail</label>
                        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 mb-1">Adresse de livraison</label>
                        <textarea name="address" rows="3" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm py-3 rounded-lg transition shadow-md">
                            Payer et valider la commande (Simulation)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Récapitulatif -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                <h3 class="font-bold text-gray-800 mb-4 text-base">Résumé du panier</h3>
                <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto mb-4">
                    <?php foreach ($cart as $item): ?>
                        <div class="py-2 flex justify-between text-xs">
                            <span class="text-gray-600"><?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)</span>
                            <span class="font-bold text-gray-900"><?= number_format($item['price'] * $item['quantity'], 2) ?> €</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                    <span class="font-bold text-sm text-gray-800">Total</span>
                    <span class="text-lg font-black text-gray-900"><?= number_format($total, 2) ?> €</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
