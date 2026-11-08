<?php
session_start();
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Calcul du panier pour le badge de la navbar
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><= htmlspecialchars($product['title']) ?> — E-SHOP</title>
    <meta name="description" content="<?= htmlspecialchars(substr($product['description'], 0, 150)) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
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

    <!-- Détail du produit -->
    <main class="max-w-6xl mx-auto px-6 py-16 flex-grow w-full">
        <!-- Fil d'ariane -->
        <div class="mb-8 text-xs font-semibold text-slate-400 flex items-center gap-2">
            <a href="index.php" class="hover:text-slate-900 transition">Accueil</a>
            <span>/</span>
            <span class="text-slate-600"><?= htmlspecialchars($product['category_name'] ?? 'Catalogue') ?></span>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-10 p-8 md:p-12 items-center">
            <!-- Image Produit -->
            <div class="h-80 md:h-[450px] bg-slate-100 rounded-2xl overflow-hidden shadow-inner relative group">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <?php if (!empty($product['category_name'])): ?>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow-sm">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Informations et Achat -->
            <div class="flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-4"><?= htmlspecialchars($product['title']) ?></h2>
                    <div class="text-3xl font-black text-orange-600 mb-6"><?= number_format($product['price'], 2) ?> €</div>
                    
                    <div class="text-slate-600 text-sm md:text-base leading-relaxed mb-8 space-y-2 border-t border-b border-slate-100 py-6">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-8">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        <span>Stock disponible : <strong class="text-slate-900"><?= $product['stock'] ?> unités</strong></span>
                    </div>
                </div>

                <!-- Formulaire d'ajout au panier -->
                <div class="pt-6 border-t border-slate-100">
                    <form action="cart_action.php" method="POST" class="flex items-center gap-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="flex items-center border border-slate-200 rounded-2xl px-3 py-1 bg-slate-50">
                            <label for="qty" class="text-xs font-bold text-slate-400 mr-2 uppercase">Qté</label>
                            <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="w-16 bg-transparent text-center text-sm font-bold text-slate-900 focus:outline-none py-2">
                        </div>
                        <button type="submit" class="flex-1 bg-slate-900 hover:bg-orange-600 text-white font-bold text-sm py-4 px-6 rounded-2xl transition shadow-md flex items-center justify-center gap-2">
                            <span>Ajouter au panier</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>


</body>
</html>
<?php include 'footer.php'; ?>