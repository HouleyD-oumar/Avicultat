<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm">Fermes</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm/show/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Lots de volailles</li>
        </ol>
    </nav>

    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Lots de volailles - <?= htmlspecialchars($farm['nom_ferme']) ?></h1>
                        <a href="<?= URLROOT ?>/batch/add/<?= $farm['id_ferme'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau lot
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <?php if (isset($stats) && $stats): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total des lots</h5>
                    <p class="card-text display-6"><?= number_format($stats['total_batches'], 0, ',', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Lots actifs</h5>
                    <p class="card-text display-6"><?= number_format($stats['active_batches'], 0, ',', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Lots vendus</h5>
                    <p class="card-text display-6"><?= number_format($stats['sold_batches'], 0, ',', ' ') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total volailles</h5>
                    <p class="card-text display-6"><?= number_format($stats['total_poultry'], 0, ',', ' ') ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Rechercher par race...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="actif" <?= ($status ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="vendu" <?= ($status ?? '') === 'vendu' ? 'selected' : '' ?>>Vendu</option>
                        <option value="perte totale" <?= ($status ?? '') === 'perte totale' ? 'selected' : '' ?>>Perte totale</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="<?= URLROOT ?>/batch/index/<?= $farm['id_ferme'] ?>" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des lots -->
    <?php if (empty($batches)): ?>
        <div class="alert alert-info">
            Aucun lot de volailles trouvé.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
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
                    <?php foreach ($batches as $batch): ?>
                        <tr>
                            <td><?= htmlspecialchars($batch['race']) ?></td>
                            <td><?= number_format($batch['effectif_initial'], 0, ',', ' ') ?></td>
                            <td><?= date('d/m/Y', strtotime($batch['date_arrivee'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $batch['statut'] === 'actif' ? 'success' : 
                                    ($batch['statut'] === 'vendu' ? 'info' : 'danger') ?>">
                                    <?= ucfirst($batch['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= URLROOT ?>/batch/show/<?= $farm['id_ferme'] ?>/<?= $batch['id_lot'] ?>" 
                                       class="btn btn-sm btn-info" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= URLROOT ?>/batch/edit/<?= $farm['id_ferme'] ?>/<?= $batch['id_lot'] ?>" 
                                       class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete(<?= $batch['id_lot'] ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce lot ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(batchId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `<?= URLROOT ?>/batch/delete/<?= $farm['id_ferme'] ?>/${batchId}`;
    modal.show();
}
</script>

<?php require_once APPROOT . '/includes/footer.php'; ?> 