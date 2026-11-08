<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Calcul du panier
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Complet — E-SHOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar / Header -->
    <header class="bg-white/85 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-1">
                E-SHOP<span class="w-2 h-2 rounded-full bg-orange-600 inline-block"></span>
            </a>
            
            <nav class="flex items-center space-x-4">
    <a href="index.php" class="text-sm font-semibold text-slate-900 hover:text-orange-600 transition">Accueil</a>
    <a href="products.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Catalogue</a>
    
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <a href="admin_orders.php" class="text-sm font-semibold text-orange-600 hover:text-orange-700 transition">Admin</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition border border-red-100 flex items-center gap-1.5">
            <span>⎋ Déconnexion</span>
        </a>
    <?php else: ?>
        <a href="login.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 transition">Connexion</a>
    <?php endif; ?>

    <a href="cart.php" class="relative bg-slate-900 hover:bg-orange-600 text-white px-5 py-2.5 rounded-full text-sm font-bold transition shadow-sm flex items-center gap-2">
        <span>Panier</span>
        <?php if ($cartCount > 0): ?>
            <span class="bg-white text-slate-900 text-xs px-2 py-0.5 rounded-full font-black"><?= $cartCount ?></span>
        <?php endif; ?>
    </a>
</nav>
        </div>
    </header>

    <!-- En-tête de page -->
    <div class="bg-slate-900 text-white py-12 px-6 text-center">
        <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">Tous nos produits</h1>
        <p class="text-slate-400 text-sm">Parcourez l'intégralité de notre catalogue disponible.</p>
    </div>

    <!-- Catalogue Complet -->
    <main class="max-w-7xl mx-auto px-6 py-16 flex-grow w-full">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-xl font-black tracking-tight text-slate-900">Tous les articles</h3>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?= count($products) ?> articles au total</span>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white p-12 rounded-2xl shadow-sm text-center border border-slate-100">
                <p class="text-slate-500 font-medium mb-4">Aucun produit n'est disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-100 flex flex-col justify-between group">
                        <div>
                            <div class="h-60 overflow-hidden bg-slate-100 relative">
                                <a href="product.php?slug=<?= $product['slug'] ?>">
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </a>
                                <?php if (!empty($product['category_name'])): ?>
                                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                                        <?= htmlspecialchars($product['category_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <h4 class="font-bold text-slate-900 text-base mb-1 group-hover:text-orange-600 transition">
                                    <a href="product.php?slug=<?= $product['slug'] ?>">
                                        <?= htmlspecialchars($product['title']) ?>
                                    </a>
                                </h4>
                                <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed"><?= htmlspecialchars($product['description']) ?></p>
                            </div>
                        </div>
                        <div class="p-5 pt-0 flex items-center justify-between mt-4">
                            <div>
                                <span class="text-xs text-slate-400 block font-semibold">Prix</span>
                                <span class="text-xl font-black text-slate-900"><?= number_format($product['price'], 2) ?> €</span>
                            </div>
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="bg-slate-900 hover:bg-orange-600 text-white text-xs font-bold px-4 py-3 rounded-xl transition shadow-sm flex items-center gap-2">
                                    <span>+ Ajouter</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
<?php include 'footer.php'; ?>