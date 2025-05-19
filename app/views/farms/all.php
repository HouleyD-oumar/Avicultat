<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container py-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/home">Tableau de bord</a></li>
            <li class="breadcrumb-item active">Toutes les fermes</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2"><?= $data['title'] ?></h1>
        <a href="<?= APP_URL ?>/home" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
    </div>
    
    <?php flash(); ?>
    
    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="searchFarm" 
                               name="search" 
                               placeholder="Rechercher une ferme..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="ownerFilter" name="owner">
                        <option value="">Tous les propriétaires</option>
                        <?php foreach ($data['owners'] ?? [] as $owner) : ?>
                            <option value="<?= $owner['id_user'] ?>" <?= ($_GET['owner'] ?? '') == $owner['id_user'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($owner['prenom'] . ' ' . $owner['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortBy" name="sort">
                        <option value="nom_ferme" <?= ($_GET['sort'] ?? '') === 'nom_ferme' ? 'selected' : '' ?>>Trier par nom</option>
                        <option value="date_creation" <?= ($_GET['sort'] ?? '') === 'date_creation' ? 'selected' : '' ?>>Trier par date</option>
                        <option value="active_batches" <?= ($_GET['sort'] ?? '') === 'active_batches' ? 'selected' : '' ?>>Trier par lots actifs</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="orderBy" name="order">
                        <option value="asc" <?= ($_GET['order'] ?? '') === 'asc' ? 'selected' : '' ?>>Ascendant</option>
                        <option value="desc" <?= ($_GET['order'] ?? '') === 'desc' ? 'selected' : '' ?>>Descendant</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($data['farms'])) : ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Aucune ferme n'a été enregistrée dans le système.
        </div>
    <?php else : ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom de la ferme</th>
                                <th>Localisation</th>
                                <th>Propriétaire</th>
                                <th>Date de création</th>
                                <th>Lots actifs</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['farms'] as $farm) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($farm['nom_ferme']) ?></td>
                                    <td><?= htmlspecialchars($farm['localisation']) ?></td>
                                    <td><?= htmlspecialchars($farm['prenom'] . ' ' . $farm['nom']) ?></td>
                                    <td><?= formatDate($farm['date_creation']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $farm['active_batches'] > 0 ? 'success' : 'secondary' ?>">
                                            <?= $farm['active_batches'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= APP_URL ?>/farm/show/<?= $farm['id_ferme'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= APP_URL ?>/farm/edit/<?= $farm['id_ferme'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (isset($data['pagination'])) : ?>
            <nav aria-label="Navigation des fermes" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($data['pagination']['current_page'] > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/farm/all?page=<?= $data['pagination']['current_page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&owner=<?= urlencode($_GET['owner'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++) : ?>
                        <li class="page-item <?= $i === $data['pagination']['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/farm/all?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&owner=<?= urlencode($_GET['owner'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']) : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= APP_URL ?>/farm/all?page=<?= $data['pagination']['current_page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&owner=<?= urlencode($_GET['owner'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>&order=<?= urlencode($_GET['order'] ?? '') ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Script de filtrage et tri -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchFarm');
    const clearSearch = document.getElementById('clearSearch');
    
    // Fonction pour mettre à jour l'URL avec les filtres
    function updateFilters() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params.set(key, value);
            }
        }
        
        // Réinitialiser la pagination
        params.delete('page');
        
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
    
    // Événements pour les filtres
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            updateFilters();
        }
    });
    
    document.getElementById('ownerFilter').addEventListener('change', updateFilters);
    document.getElementById('sortBy').addEventListener('change', updateFilters);
    document.getElementById('orderBy').addEventListener('change', updateFilters);
    
    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        updateFilters();
    });
});
</script>

<?php require_once APPROOT . '/includes/footer.php'; ?>