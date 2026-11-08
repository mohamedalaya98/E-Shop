<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Sécurité : Accès réservé aux administrateurs
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: admin_orders.php');
    exit;
}

// Récupération des données actuelles du produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: admin_orders.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $image = trim($_POST['image'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Génération d'un slug mis à jour
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    if (empty($title) || empty($price) || empty($category_id)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $updateStmt = $pdo->prepare("
            UPDATE products 
            SET category_id = ?, title = ?, slug = ?, description = ?, price = ?, stock = ?, image = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$category_id, $title, $slug, $description, $price, $stock, $image, $productId]);
        
        $success = "Le produit a été mis à jour avec succès !";
        
        // Recharger les données fraîches
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit #<?= $product['id'] ?> — Admin</title>
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
                <a href="admin_orders.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Commandes & Produits</a>
                <a href="admin_add.php" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Ajouter un produit</a>
                <a href="index.php" class="text-sm font-semibold text-slate-500 hover:text-slate-900 transition">← Retour boutique</a>
                <a href="logout.php" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition border border-red-100">Déconnexion</a>
            </nav>
        </div>
    </header>

    <!-- Formulaire d'édition -->
    <main class="max-w-3xl mx-auto px-6 py-12 flex-grow w-full">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Modifier le produit</h2>
                <p class="text-slate-500 text-xs mt-1">Édition des informations de : <span class="font-bold text-slate-800"><?= htmlspecialchars($product['title']) ?></span></p>
            </div>
            <a href="admin_orders.php" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition">← Annuler</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold rounded-2xl flex items-center gap-2">
                <span>✓</span> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-2xl flex items-center gap-2">
                <span>✕</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 md:p-10">
            <form action="admin_edit.php?id=<?= $product['id'] ?>" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Titre du produit</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Catégorie</label>
                        <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Prix (€)</label>
                        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Stock disponible</label>
                        <input type="number" name="stock" value="<?= $product['stock'] ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-400 mb-2">URL de l'image</label>
                        <input type="url" name="image" value="<?= htmlspecialchars($product['image']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Description</label>
                    <textarea name="description" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-semibold text-slate-900 focus:outline-none focus:border-orange-500 transition"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm py-4 rounded-2xl transition shadow-md">
                    Enregistrer les modifications
                </button>
            </form>
        </div>
    </main>

</body>
</html>

<?php include 'footer.php'; ?>