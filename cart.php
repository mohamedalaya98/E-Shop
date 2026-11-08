<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier - E-SHOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-black tracking-tight text-gray-900"><a href="index.php">E-SHOP<span class="text-orange-600">.</span></a></h1>
            <a href="index.php" class="text-sm font-medium text-orange-600 hover:underline">← Continuer les achats</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-extrabold text-gray-800 mb-6">Votre Panier</h2>

        <?php if (empty($cart)): ?>
            <div class="bg-white p-8 rounded-xl shadow-sm text-center">
                <p class="text-gray-500 mb-4">Votre panier est vide pour le moment.</p>
                <a href="index.php" class="inline-block bg-orange-600 text-white font-semibold text-sm px-6 py-3 rounded-lg hover:bg-orange-700 transition">
                    Découvrir nos produits
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
                <div class="divide-y divide-gray-100">
                    <?php foreach ($cart as $item): ?>
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center space-x-4 w-full sm:w-auto">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div>
                                    <h3 class="font-bold text-gray-800"><?= htmlspecialchars($item['title']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= number_format($item['price'], 2) ?> € / unité</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-6 w-full sm:w-auto justify-between sm:justify-end">
                                <form action="cart_action.php" method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 border border-gray-300 rounded-lg p-1.5 text-center text-sm font-semibold">
                                    <button type="submit" class="text-xs text-gray-600 hover:text-black bg-gray-100 px-2 py-1.5 rounded">Modifier</button>
                                </form>

                                <span class="font-bold text-gray-900 w-24 text-right">
                                    <?= number_format($item['price'] * $item['quantity'], 2) ?> €
                                </span>

                                <a href="cart_action.php?action=remove&product_id=<?= $item['id'] ?>" class="text-red-500 hover:text-red-700 text-sm font-bold">✕</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="cart_action.php?action=clear" class="text-sm text-red-600 hover:underline">Vider le panier</a>
                
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <span class="text-xs text-gray-500 block uppercase font-bold">Total Général</span>
                        <span class="text-2xl font-black text-gray-900"><?= number_format($total, 2) ?> €</span>
                    </div>
                    <a href="checkout.php" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-3 rounded-lg transition shadow-md inline-block text-center">
                        Passer la commande →
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
