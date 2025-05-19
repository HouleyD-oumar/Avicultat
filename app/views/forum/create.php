<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Nouveau Sujet</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/forum" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au forum
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <form action="<?= BASE_URL ?>/forum/store" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du sujet</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                            <div class="invalid-feedback">
                                Veuillez entrer un titre
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="general">Général</option>
                                <option value="sante">Santé</option>
                                <option value="alimentation">Alimentation</option>
                                <option value="equipement">Équipement</option>
                                <option value="conseils">Conseils</option>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner une catégorie
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            <div class="invalid-feedback">
                                Veuillez entrer le contenu du sujet
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Publier
                            </button>
                            <a href="<?= BASE_URL ?>/forum" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Conseils pour un bon sujet</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Soyez clair et précis dans votre titre
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Choisissez la bonne catégorie
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Structurez votre message
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success"></i>
                                    Restez courtois et professionnel
                                </li>
                                <li>
                                    <i class="fas fa-check-circle text-success"></i>
                                    Vérifiez l'orthographe
                                </li>
                            </ul>
                        </div>
                    </div>
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