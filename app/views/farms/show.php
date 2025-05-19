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
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/farm">Mes fermes</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($data['farm']['nom_ferme']) ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= $data['title'] ?></h1>
        <div>
            <a href="<?= APP_URL ?>/farm" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
            <a href="<?= APP_URL ?>/farm/edit/<?= $data['farm']['id_ferme'] ?>" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
        </div>
    </div>

    <?php flash(); ?>

    <div class="row">
        <!-- Informations de la ferme -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informations générales</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nom de la ferme</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($data['farm']['nom_ferme']) ?></dd>

                        <dt class="col-sm-4">Localisation</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($data['farm']['localisation']) ?></dd>

                        <dt class="col-sm-4">Date de création</dt>
                        <dd class="col-sm-8"><?= formatDate($data['farm']['date_creation']) ?></dd>

                        <dt class="col-sm-4">Propriétaire</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($data['farm']['prenom'] . ' ' . $data['farm']['nom']) ?></dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($data['farm']['email']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-kiwi-bird text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Lots actifs</h6>
                                    <h3 class="mb-0"><?= $data['active_batches'] ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des lots -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Lots de volailles</h5>
                <a href="<?= APP_URL ?>/batch/add/<?= $data['farm']['id_ferme'] ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Ajouter un lot
                </a>
            </div>

            <?php if (empty($data['batches'])) : ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucun lot de volailles n'a été créé pour cette ferme.
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Race</th>
                                <th>Effectif initial</th>
                                <th>Date d'arrivée</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['batches'] as $batch) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($batch['race']) ?></td>
                                    <td><?= $batch['effectif_initial'] ?></td>
                                    <td><?= formatDate($batch['date_arrivee']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusBadgeClass($batch['statut']) ?>">
                                            <?= ucfirst($batch['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= APP_URL ?>/batch/show/<?= $data['farm']['id_ferme'] ?>/<?= $batch['id_lot'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= APP_URL ?>/batch/edit/<?= $data['farm']['id_ferme'] ?>/<?= $batch['id_lot'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once $footerPath; ?>