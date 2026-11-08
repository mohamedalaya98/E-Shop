<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Calcul du nombre total d'articles dans le panier
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

$pdo = getDBConnection();

// Récupération des 8 produits les plus récents pour l'accueil
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 8");
$products = $stmt->fetchAll();

// Compter le nombre total de produits pour afficher la métrique du catalogue
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SHOP — Boutique en Ligne</title>
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

    <!-- Hero Section Moderne avec Image de Fond -->
    <section class="relative py-28 px-6 text-center overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1600" alt="Hero Background" class="w-full h-full object-cover object-center opacity-30 scale-105 transform">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-slate-950"></div>
        </div>

        <div class="max-w-3xl mx-auto relative z-10">
            <span class="bg-orange-500/10 text-orange-400 border border-orange-500/20 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full inline-block mb-4 shadow-sm">
                Nouvelle Collection 2026
            </span>
            <h2 class="text-4xl md:text-6xl font-black tracking-tight text-white mb-6">
                Le style et l'innovation <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-200">réunis</span>
            </h2>
            <p class="text-slate-300 text-base md:text-lg mb-8 font-medium leading-relaxed">
                Découvrez notre sélection exclusive de produits haut de gamme conçus pour sublimer votre quotidien.
            </p>
            <a href="products.php" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-8 py-4 rounded-2xl transition shadow-xl hover:shadow-orange-600/20">
                Voir tous les produits →
            </a>
        </div>
    </section>

    <!-- Bandeau de Réassurance -->
    <section class="bg-white border-b border-slate-100 py-8">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-6 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-4 p-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold flex-shrink-0">🚚</div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Livraison Rapide</h4>
                    <p class="text-slate-500 text-xs">Expédié en 24/48h</p>
                </div>
            </div>
            <div class="flex items-center justify-center md:justify-start gap-4 p-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold flex-shrink-0">🔒</div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Paiement Sécurisé</h4>
                    <p class="text-slate-500 text-xs">Transactions 100% sécurisées</p>
                </div>
            </div>
            <div class="flex items-center justify-center md:justify-start gap-4 p-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold flex-shrink-0">↩️</div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Retours Faciles</h4>
                    <p class="text-slate-500 text-xs">30 jours pour changer d'avis</p>
                </div>
            </div>
            <div class="flex items-center justify-center md:justify-start gap-4 p-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold flex-shrink-0">💬</div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Support 7j/7</h4>
                    <p class="text-slate-500 text-xs">Une équipe à votre écoute</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Catalogue Produits (Aperçu des 8 derniers) -->
    <main class="max-w-7xl mx-auto px-6 py-16 flex-grow w-full">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h3 class="text-2xl font-black tracking-tight text-slate-900">Nouveautés</h3>
                <p class="text-slate-500 text-xs mt-1">Nos derniers articles ajoutés au catalogue</p>
            </div>
            <a href="products.php" class="text-xs font-bold text-orange-600 hover:underline">Voir tout le catalogue (<?= $totalProducts ?>) →</a>
        </div>

        <?php if (empty($products)): ?>
            <div class="bg-white p-12 rounded-2xl shadow-sm text-center border border-slate-100">
                <p class="text-slate-500 font-medium mb-4">Aucun produit n'est disponible pour le moment.</p>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin_add.php" class="inline-block bg-orange-600 text-white font-bold text-sm px-6 py-3 rounded-xl hover:bg-orange-700 transition">
                        Ajouter un produit
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-100 flex flex-col justify-between group">
                        <div>
                            <!-- Image avec zoom au survol -->
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

                            <!-- Infos Produit -->
                            <div class="p-5">
                                <h4 class="font-bold text-slate-900 text-base mb-1 group-hover:text-orange-600 transition">
                                    <a href="product.php?slug=<?= $product['slug'] ?>">
                                        <?= htmlspecialchars($product['title']) ?>
                                    </a>
                                </h4>
                                <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed"><?= htmlspecialchars($product['description']) ?></p>
                            </div>
                        </div>

                        <!-- Prix & Ajout au Panier -->
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
            
            <div class="text-center mt-12">
                <a href="products.php" class="inline-block bg-white hover:bg-slate-50 text-slate-900 border border-slate-200 font-bold text-sm px-8 py-4 rounded-2xl transition shadow-sm">
                    Découvrir tous les produits →
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Section Newsletter -->
    <section class="bg-slate-900 text-white py-16 px-6 my-12">
        <div class="max-w-4xl mx-auto text-center">
            <h3 class="text-2xl md:text-3xl font-black tracking-tight mb-3">Rejoignez notre communauté</h3>
            <p class="text-slate-400 text-sm mb-8 max-w-lg mx-auto">Recevez nos offres exclusives, nos nouveautés en avant-première et profitez d'un code promo sur votre première commande.</p>
            <form onsubmit="event.preventDefault(); alert('Merci pour votre inscription !');" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <input type="email" placeholder="Votre adresse e-mail" required class="flex-grow bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-orange-500">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition shadow-md">
                    S'inscrire
                </button>
            </form>
        </div>
    </section>

</body>
</html>
<?php include 'footer.php'; ?>