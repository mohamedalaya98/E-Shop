<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$orders = $pdo->query("
    SELECT o.*, u.name as customer_name, u.email as customer_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Commandes — Admin</title>
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
                <a href="admin_orders.php" class="text-sm font-bold text-orange-600 transition">Commandes</a>
                <a href="admin_products.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Produits</a>
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
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Suivi des commandes</h2>
                <p class="text-slate-500 text-xs mt-1">Consultez l'historique et le statut des commandes clients.</p>
            </div>
            <span class="bg-white border border-slate-200 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl shadow-sm">
                Total : <?= count($orders) ?> commande(s)
            </span>
        </div>

        <?php if (empty($orders)): ?>
            <div class="bg-white p-16 rounded-3xl shadow-sm text-center border border-slate-100">
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">📦</div>
                <h3 class="font-bold text-slate-900 text-base mb-1">Aucune commande pour le moment</h3>
                <p class="text-slate-500 text-xs">Les commandes validées par les clients apparaîtront ici.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-5">Référence</th>
                                <th class="p-5">Client</th>
                                <th class="p-5">Date</th>
                                <th class="p-5">Montant Total</th>
                                <th class="p-5">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-5 font-black text-slate-900">#<?= $order['id'] ?></td>
                                    <td class="p-5">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <div class="text-xs text-slate-400"><?= htmlspecialchars($order['customer_email']) ?></div>
                                    </td>
                                    <td class="p-5 text-slate-500 text-xs font-medium"><?= $order['created_at'] ?></td>
                                    <td class="p-5 font-black text-slate-900 text-base"><?= number_format($order['total_amount'], 2) ?> €</td>
                                    <td class="p-5">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?= htmlspecialchars($order['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
<?php include 'footer.php'; ?>