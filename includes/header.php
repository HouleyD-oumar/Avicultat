<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?= APP_URL ?>">
                    <i class="fas fa-feather-alt me-2"></i><?= APP_NAME ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>">Accueil</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($_SESSION['user_role'], ['eleveur', 'admin'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/farm">Mes Fermes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/poultryBatch">Lots de Volailles</a>
                            </li>
                            <?php endif; ?>
                            <?php if (in_array($_SESSION['user_role'], ['veterinaire', 'eleveur', 'admin'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/treatment">Traitements</a>
                            </li>
                            <?php endif; ?>
                            <?php if (in_array($_SESSION['user_role'], ['eleveur', 'admin'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/feed">Alimentation</a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/forum">Forum</a>
                            </li>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/admin">Administration</a>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/user/profile">Mon Profil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/user/logout">Déconnexion</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/user/login">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>/user/register">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['flash']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if (isset($title)): ?>
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>"><i class="fas fa-home"></i></a></li>
                <?php
                $url = trim($_SERVER['REQUEST_URI'], '/');
                $segments = explode('/', $url);
                $currentPath = '';
                
                foreach ($segments as $segment) {
                    if ($segment === 'index.php') continue;
                    $currentPath .= '/' . $segment;
                    $isLast = ($segment === end($segments));
                    
                    if ($isLast) {
                        echo '<li class="breadcrumb-item active" aria-current="page">' . ucfirst($segment) . '</li>';
                    } else {
                        echo '<li class="breadcrumb-item"><a href="' . APP_URL . $currentPath . '">' . ucfirst($segment) . '</a></li>';
                    }
                }
                ?>
            </ol>
        </nav>
        <?php endif; ?>