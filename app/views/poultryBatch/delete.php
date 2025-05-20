<?php require_once APPROOT . '/includes/header.php'; ?>

<div class="container mt-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm">Fermes</a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/farm/show/<?= $farm['id_ferme'] ?>"><?= htmlspecialchars($farm['nom_ferme']) ?></a></li>
            <li class="breadcrumb-item"><a href="<?= URLROOT ?>/batch/index/<?= $farm['id_ferme'] ?>">Lots de volailles</a></li>
            <li class="breadcrumb-item active" aria-current="page">Supprimer le lot</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">Confirmer la suppression</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h3 class="h5 mb-3">Attention !</h3>
                        <p>Vous êtes sur le point de supprimer le lot suivant :</p>
                        <ul class="mb-0">
                            <li>Race : <?= htmlspecialchars($batch['race']) ?></li>
                            <li>Effectif initial : <?= number_format($batch['effectif_initial'], 0, ',', ' ') ?></li>
                            <li>Date d'arrivée : <?= date('d/m/Y', strtotime($batch['date_arrivee'])) ?></li>
                            <li>Statut : <?= ucfirst($batch['statut']) ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cette action est irréversible. Toutes les données associées à ce lot (traitements, alimentations, etc.) seront également supprimées.
                    </div>

                    <form method="POST" action="<?= URLROOT ?>/batch/delete/<?= $farm['id_ferme'] ?>/<?= $batch['id_lot'] ?>" class="mt-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <div class="d-flex justify-content-between">
                            <a href="<?= URLROOT ?>/batch/show/<?= $farm['id_ferme'] ?>/<?= $batch['id_lot'] ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Confirmer la suppression
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/includes/footer.php'; ?> 