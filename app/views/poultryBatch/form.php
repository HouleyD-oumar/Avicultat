<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm">Fermes</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm/show/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/batch/index/<?= $farm['id_ferme'] ?>">Lots de volailles</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= isset($batch['id_lot']) ? 'Modifier le lot' : 'Nouveau lot' ?>
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">
                        <?= isset($batch['id_lot']) ? 'Modifier le lot' : 'Nouveau lot' ?>
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= isset($batch['id_lot']) ? URLROOT . '/batch/update/' . $farm['id_ferme'] . '/' . $batch['id_lot'] : URLROOT . '/batch/create/' . $farm['id_ferme'] ?>" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="race" class="form-label">Race</label>
                            <input type="text" class="form-control <?= isset($errors['race']) ? 'is-invalid' : '' ?>" 
                                   id="race" name="race" 
                                   value="<?= htmlspecialchars(isset($batch['race']) ? $batch['race'] : '') ?>" 
                                   required minlength="3" maxlength="100">
                            <?php if (isset($errors['race'])): ?>
                                <div class="invalid-feedback"><?= $errors['race'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="effectif_initial" class="form-label">Effectif initial</label>
                            <input type="number" class="form-control <?= isset($errors['effectif_initial']) ? 'is-invalid' : '' ?>" 
                                   id="effectif_initial" name="effectif_initial" 
                                   value="<?= htmlspecialchars(isset($batch['effectif_initial']) ? $batch['effectif_initial'] : '') ?>" 
                                   required min="1">
                            <?php if (isset($errors['effectif_initial'])): ?>
                                <div class="invalid-feedback"><?= $errors['effectif_initial'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="date_arrivee" class="form-label">Date d'arrivée</label>
                            <input type="date" class="form-control <?= isset($errors['date_arrivee']) ? 'is-invalid' : '' ?>" 
                                   id="date_arrivee" name="date_arrivee" 
                                   value="<?= htmlspecialchars(isset($batch['date_arrivee']) ? $batch['date_arrivee'] : '') ?>" 
                                   required max="<?= date('Y-m-d') ?>">
                            <?php if (isset($errors['date_arrivee'])): ?>
                                <div class="invalid-feedback"><?= $errors['date_arrivee'] ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($batch['id_lot'])): ?>
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select <?= isset($errors['statut']) ? 'is-invalid' : '' ?>" 
                                        id="statut" name="statut" required>
                                    <option value="actif" <?= (isset($batch['statut']) && $batch['statut'] === 'actif') ? 'selected' : '' ?>>Actif</option>
                                    <option value="vendu" <?= (isset($batch['statut']) && $batch['statut'] === 'vendu') ? 'selected' : '' ?>>Vendu</option>
                                    <option value="perte totale" <?= (isset($batch['statut']) && $batch['statut'] === 'perte totale') ? 'selected' : '' ?>>Perte totale</option>
                                </select>
                                <?php if (isset($errors['statut'])): ?>
                                    <div class="invalid-feedback"><?= $errors['statut'] ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <a href="<?= URLROOT ?>/batch/index/<?= $farm['id_ferme'] ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?= isset($batch['id_lot']) ? 'Mettre à jour' : 'Créer' ?>
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

<?php require_once APPROOT . '/includes/footer.php'; ?> 