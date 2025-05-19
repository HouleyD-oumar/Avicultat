<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Nouveau Traitement</h1>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <form action="<?= BASE_URL ?>/treatments/store" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="batch_id" class="form-label">Lot de Volailles</label>
                            <select class="form-select" id="batch_id" name="batch_id" required>
                                <option value="">Sélectionner un lot</option>
                                <?php foreach ($batches as $batch): ?>
                                    <option value="<?= $batch['id'] ?>">
                                        <?= htmlspecialchars($batch['name']) ?> 
                                        (<?= date('d/m/Y', strtotime($batch['start_date'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner un lot
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="treatment_type" class="form-label">Type de Traitement</label>
                            <select class="form-select" id="treatment_type" name="treatment_type" required>
                                <option value="">Sélectionner un type</option>
                                <option value="vaccination">Vaccination</option>
                                <option value="medication">Médication</option>
                                <option value="vitamin">Complément Vitaminé</option>
                                <option value="other">Autre</option>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner un type de traitement
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="treatment_date" class="form-label">Date du Traitement</label>
                            <input type="date" class="form-control" id="treatment_date" name="treatment_date" required>
                            <div class="invalid-feedback">
                                Veuillez sélectionner une date
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dosage" class="form-label">Dosage</label>
                            <input type="text" class="form-control" id="dosage" name="dosage" required>
                            <div class="invalid-feedback">
                                Veuillez indiquer le dosage
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a href="<?= BASE_URL ?>/treatments" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Validation du formulaire
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include('partials/footer.php'); ?> 