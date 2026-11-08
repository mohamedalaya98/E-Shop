<?php
session_start();
require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $pdo = getDBConnection();
        
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet e-mail est déjà utilisé.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $hashedPassword]);

            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - E-SHOP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-8">
            <a href="index.php" class="text-2xl font-black tracking-tight text-slate-900 inline-block mb-2">
                E-SHOP<span class="w-2 h-2 rounded-full bg-orange-600 inline-block"></span>
            </a>
            <h1 class="text-xl font-bold text-slate-900">Créer un compte</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 mb-6 text-xs text-red-600 bg-red-50 border border-red-100 rounded-xl font-semibold">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Nom complet</label>
                <input type="text" name="name" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Adresse e-mail</label>
                <input type="email" name="email" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-600 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Mot de passe</label>
                <input type="password" name="password" required class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-orange-600 focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-bold text-sm py-3.5 rounded-xl transition shadow-md">
                S'inscrire
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Déjà un compte ? <a href="login.php" class="text-orange-600 font-bold hover:underline">Se connecter</a>
        </div>
    </div>

</body>
</html>