<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$products = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Header Admin -->
    <header class="bg-white/85 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-2xl font-black tracking-tight text-slate-900 flex items-center gap-1">
                    E-SHOP<span class="w-2 h-2 rounded-full bg-orange-600 inline-block"></span>
                </a>
                <span class="bg-slate-900 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md tracking-wider">Back-Office</span>
            </div>
            
            <nav class="flex items-center space-x-6">
                <a href="admin_orders.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Commandes</a>
                <a href="admin_products.php" class="text-sm font-bold text-orange-600 transition">Produits</a>
                <a href="admin_add.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Ajouter</a>
                <a href="index.php" class="text-sm font-semibold text-slate-500 hover:text-slate-900 transition">← Boutique</a>
                <a href="logout.php" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition border border-red-100">Déconnexion</a>
            </nav>
        </div>
    </header>

    <!-- Contenu -->
    <main class="max-w-7xl mx-auto px-6 py-12 flex-grow w-full">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Catalogue des produits</h2>
                <p class="text-slate-500 text-xs mt-1">Modifiez ou mettez à jour les articles de la boutique.</p>
            </div>
            <a href="admin_add.php" class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-5 py-3 rounded-xl transition shadow-sm">
                + Ajouter un produit
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-5">Produit</th>
                            <th class="p-5">Catégorie</th>
                            <th class="p-5">Prix</th>
                            <th class="p-5">Stock</th>
                            <th class="p-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach ($products as $p): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-5 font-bold text-slate-900 flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($p['image']) ?>" class="w-10 h-10 rounded-lg object-cover bg-slate-100">
                                    <span><?= htmlspecialchars($p['title']) ?></span>
                                </td>
                                <td class="p-5 text-slate-500 text-xs"><?= htmlspecialchars($p['category_name'] ?? 'Non classé') ?></td>
                                <td class="p-5 font-black text-slate-900"><?= number_format($p['price'], 2) ?> €</td>
                                <td class="p-5">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $p['stock'] > 5 ? 'bg-slate-100 text-slate-700' : 'bg-amber-50 text-amber-700' ?>">
                                        <?= $p['stock'] ?> en stock
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <a href="admin_edit.php?id=<?= $p['id'] ?>" class="inline-block bg-slate-900 hover:bg-orange-600 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                        Éditer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>


</body>
</html>
<?php include 'footer.php'; ?>