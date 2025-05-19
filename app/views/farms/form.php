<?php require_once APPROOT . '/app/views/inc/header.php'; ?>

<div class="container py-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/home">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/farm">Mes fermes</a></li>
            <li class="breadcrumb-item active"><?= $data['title'] ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h2 mb-4"><?= $data['title'] ?></h1>
                    
                    <form action="<?= APP_URL ?>/farm/<?= isset($data['id_ferme']) ? 'update/' . $data['id_ferme'] : 'create' ?>" 
                          method="POST" 
                          id="farmForm" 
                          class="needs-validation" 
                          novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCSRFToken() ?>">
                        
                        <div class="mb-3">
                            <label for="nom_ferme" class="form-label">Nom de la ferme</label>
                            <input type="text" 
                                   class="form-control <?= isset($data['errors']['nom_ferme']) ? 'is-invalid' : '' ?>" 
                                   id="nom_ferme" 
                                   name="nom_ferme" 
                                   value="<?= htmlspecialchars($data['nom_ferme']) ?>" 
                                   required
                                   minlength="3"
                                   maxlength="100">
                            <div class="invalid-feedback">
                                <?= isset($data['errors']['nom_ferme']) ? $data['errors']['nom_ferme'] : 'Le nom de la ferme est requis (3-100 caractères)' ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="localisation" class="form-label">Localisation</label>
                            <input type="text" 
                                   class="form-control <?= isset($data['errors']['localisation']) ? 'is-invalid' : '' ?>" 
                                   id="localisation" 
                                   name="localisation" 
                                   value="<?= htmlspecialchars($data['localisation']) ?>" 
                                   required
                                   minlength="5"
                                   maxlength="255">
                            <div class="invalid-feedback">
                                <?= isset($data['errors']['localisation']) ? $data['errors']['localisation'] : 'La localisation est requise (5-255 caractères)' ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_creation" class="form-label">Date de création</label>
                            <input type="date" 
                                   class="form-control <?= isset($data['errors']['date_creation']) ? 'is-invalid' : '' ?>" 
                                   id="date_creation" 
                                   name="date_creation" 
                                   value="<?= htmlspecialchars($data['date_creation']) ?>" 
                                   required
                                   max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">
                                <?= isset($data['errors']['date_creation']) ? $data['errors']['date_creation'] : 'La date de création est requise et ne peut pas être dans le futur' ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= APP_URL ?>/farm" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i><?= isset($data['id_ferme']) ? 'Mettre à jour' : 'Créer' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de validation du formulaire -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('farmForm');
    let formChanged = false;

    // Détecter les changements dans le formulaire
    form.addEventListener('change', function() {
        formChanged = true;
    });

    // Confirmation avant de quitter si des changements ont été faits
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Validation du formulaire
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php require_once APPROOT . '/app/views/inc/footer.php'; ?>