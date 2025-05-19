<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="/farms">Fermes</a></li>
            <li class="breadcrumb-item"><a href="/farms/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Lots de volailles</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lots de volailles - <?= htmlspecialchars($farm['nom_ferme']) ?></h1>
        <a href="/farms/<?= $farm['id_ferme'] ?>/batches/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau lot
        </a>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Rechercher par race...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="actif" <?= $status === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="vendu" <?= $status === 'vendu' ? 'selected' : '' ?>>Vendu</option>
                        <option value="perte totale" <?= $status === 'perte totale' ? 'selected' : '' ?>>Perte totale</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

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
                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>" 
                                       class="btn btn-sm btn-info" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/edit" 
                                       class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/farms/<?= $farm['id_ferme'] ?>/batches/<?= $batch['id_lot'] ?>/delete" 
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

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Pagination des lots">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
                            Précédent
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>">
                            Suivant
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/includes/footer.php'; ?> 