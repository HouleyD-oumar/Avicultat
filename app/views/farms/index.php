<?php 
$headerPath = APPROOT . '/includes/header.php';
$footerPath = APPROOT . '/includes/footer.php';

if (!file_exists($headerPath)) {
    die('Erreur : Le fichier header.php n\'existe pas à l\'emplacement : ' . $headerPath);
}
if (!file_exists($footerPath)) {
    die('Erreur : Le fichier footer.php n\'existe pas à l\'emplacement : ' . $footerPath);
}

require_once $headerPath; 
?>

<div class="container py-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/home">Tableau de bord</a></li>
            <li class="breadcrumb-item active">Mes fermes</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= $data['title'] ?></h1>
        <div>
            <a href="<?= APP_URL ?>/home" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
            <a href="<?= APP_URL ?>/farm/add" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Ajouter une ferme
            </a>
        </div>
    </div>
    
    <?php flash(); ?>
    
    <?php if (empty($data['farms'])) : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Vous n'avez pas encore de ferme. Cliquez sur "Ajouter une ferme" pour commencer.
        </div>
    <?php else : ?>
        <div class="row">
            <?php foreach ($data['farms'] as $farm) : ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($farm['nom_ferme']) ?></h5>
                            <p class="card-text text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($farm['localisation']) ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> Créée le <?= formatDate($farm['date_creation']) ?>
                                </small>
                            </p>
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-<?= $farm['active_batches'] > 0 ? 'success' : 'secondary' ?> me-2">
                                    <?= $farm['active_batches'] ?> lot<?= $farm['active_batches'] > 1 ? 's' : '' ?> actif<?= $farm['active_batches'] > 1 ? 's' : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <div class="btn-group w-100">
                                <a href="<?= APP_URL ?>/farm/show/<?= $farm['id_ferme'] ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Détails
                                </a>
                                <a href="<?= APP_URL ?>/farm/edit/<?= $farm['id_ferme'] ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($data['pagination'])) : ?>
            <nav aria-label="Navigation des fermes" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($data['pagination']['current_page'] > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/farm?page=<?= $data['pagination']['current_page'] - 1 ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++) : ?>
                        <li class="page-item <?= $i === $data['pagination']['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/farm?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']) : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/farm?page=<?= $data['pagination']['current_page'] + 1 ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once $footerPath; ?>