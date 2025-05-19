<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="/farms">Fermes</a></li>
            <li class="breadcrumb-item"><a href="/farms/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item"><a href="/farms/<?= $farm['id_ferme'] ?>/batches">Lots de volailles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du lot</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Informations du lot -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">Informations du lot</h2>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Race</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($batch['race']) ?></dd>

                        <dt class="col-sm-4">Effectif initial</dt>
                        <dd class="col-sm-8"><?= number_format($batch['effectif_initial'], 0, ',', ' ') ?></dd>

                        <dt class="col-sm-4">Date d'arrivée</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($batch['date_arrivee'])) ?></dd>

                        <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?= $batch['statut'] === 'actif' ? 'success' : 
                                ($batch['statut'] === 'vendu' ? 'info' : 'danger') ?>">
                                <?= ucfirst($batch['statut']) ?>
                            </span>
                        </dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <div class="btn-group w-100">
                        <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/edit" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/delete" 
                           class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">Historique</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($history)): ?>
                        <div class="alert alert-info">
                            Aucun historique disponible.
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $event): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-<?= $event['type'] === 'traitement' ? 'primary' : 'success' ?>"></div>
                                    <div class="timeline-content">
                                        <h3 class="timeline-title">
                                            <?= ucfirst($event['type']) ?> - <?= date('d/m/Y', strtotime($event['date'])) ?>
                                        </h3>
                                        <p class="mb-0">
                                            <strong><?= htmlspecialchars($event['description']) ?></strong>
                                            <?php if ($event['details']): ?>
                                                <br>
                                                <?= htmlspecialchars($event['details']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Traitements et alimentations -->
    <div class="row mt-4">
        <!-- Traitements -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h4 mb-0">Traitements</h2>
                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/treatments/create" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouveau traitement
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($treatments)): ?>
                        <div class="alert alert-info">
                            Aucun traitement enregistré.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Produit</th>
                                        <th>Observations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($treatments as $treatment): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($treatment['date_application'])) ?></td>
                                            <td><?= htmlspecialchars($treatment['produit']) ?></td>
                                            <td><?= htmlspecialchars($treatment['observations']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/treatments/<?= $treatment['id_traitement'] ?>/edit" 
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/treatments/<?= $treatment['id_traitement'] ?>/delete" 
                                                       class="btn btn-sm btn-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
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

        <!-- Alimentations -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h4 mb-0">Alimentations</h2>
                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/feeds/create" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle alimentation
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($feeds)): ?>
                        <div class="alert alert-info">
                            Aucune alimentation enregistrée.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Quantité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feeds as $feed): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($feed['date_distribution'])) ?></td>
                                            <td><?= htmlspecialchars($feed['type']) ?></td>
                                            <td><?= number_format($feed['quantite'], 2, ',', ' ') ?> kg</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/feeds/<?= $feed['id_alimentation'] ?>/edit" 
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/feeds/<?= $feed['id_alimentation'] ?>/delete" 
                                                       class="btn btn-sm btn-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
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
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 15px;
    height: 15px;
    border-radius: 50%;
}

.timeline-content {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.timeline-title {
    font-size: 1rem;
    margin-bottom: 10px;
}
</style>

<?php require_once APPROOT . '/includes/footer.php'; ?> 