<?php require_once 'app/views/partials/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="/farms">Fermes</a></li>
            <li class="breadcrumb-item"><a href="/farms/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item"><a href="/farms/<?= $farm['id_ferme'] ?>/batches">Lots de volailles</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $batch ? 'Modifier le lot' : 'Nouveau lot' ?>
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">
                        <?= $batch ? 'Modifier le lot' : 'Nouveau lot' ?>
                    </h2>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="race" class="form-label">Race</label>
                            <input type="text" class="form-control" id="race" name="race" 
                                   value="<?= htmlspecialchars($batch['race'] ?? '') ?>" 
                                   required minlength="3" maxlength="100">
                            <div class="invalid-feedback">
                                La race doit contenir entre 3 et 100 caractères.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="effectif_initial" class="form-label">Effectif initial</label>
                            <input type="number" class="form-control" id="effectif_initial" name="effectif_initial" 
                                   value="<?= htmlspecialchars($batch['effectif_initial'] ?? '') ?>" 
                                   required min="1">
                            <div class="invalid-feedback">
                                L'effectif initial doit être supérieur à 0.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="date_arrivee" class="form-label">Date d'arrivée</label>
                            <input type="date" class="form-control" id="date_arrivee" name="date_arrivee" 
                                   value="<?= htmlspecialchars($batch['date_arrivee'] ?? '') ?>" 
                                   required max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">
                                La date d'arrivée ne peut pas être dans le futur.
                            </div>
                        </div>

                        <?php if ($batch): ?>
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif" <?= ($batch['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                                    <option value="vendu" <?= ($batch['statut'] ?? '') === 'vendu' ? 'selected' : '' ?>>Vendu</option>
                                    <option value="perte totale" <?= ($batch['statut'] ?? '') === 'perte totale' ? 'selected' : '' ?>>Perte totale</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <a href="/farms/<?= $farm['id_ferme'] ?>/batches" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?= $batch ? 'Mettre à jour' : 'Créer' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation du formulaire
(function() {
    'use strict';
    
    const form = document.querySelector('.needs-validation');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
    
    // Empêcher la soumission si on quitte la page avec des modifications
    let formChanged = false;
    
    form.addEventListener('change', function() {
        formChanged = true;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
})();
</script>

<?php require_once 'app/views/partials/footer.php'; ?> 