<?php
session_start();
$orderId = $_SESSION['last_order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande Validée - E-SHOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-xl shadow-md p-8 text-center border border-gray-100">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
            ✓
        </div>
        <h1 class="text-2xl font-black text-gray-900 mb-2">Merci pour votre commande !</h1>
        <p class="text-gray-600 text-sm mb-6">
            <?php if ($orderId): ?>
                Votre commande n°<strong><?= $orderId ?></strong> a été enregistrée avec succès et est en cours de traitement.
            <?php else: ?>
                Votre commande a bien été validée.
            <?php endif; ?>
        </p>
        <a href="index.php" class="inline-block bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm px-6 py-3 rounded-lg transition shadow-sm">
            Retour à la boutique
        </a>
    </div>

</body>
</html>