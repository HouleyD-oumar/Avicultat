<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Tableau de bord</h2>
                <p class="card-text">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= ucfirst(htmlspecialchars($_SESSION['user_role'])) ?>)</p>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['user_role'] === 'eleveur'): ?>
<!-- TABLEAU DE BORD ÉLEVEUR -->
<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-farm me-2"></i>Mes Fermes</h3>
            </div>
            <div class="card-body">
                <?php if (empty($farms)): ?>
                    <p class="text-muted">Vous n'avez pas encore de ferme.</p>
                    <a href="<?= APP_URL ?>/farm/create" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajouter une ferme
                    </a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Localisation</th>
                                    <th>Lots actifs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($farms as $farm): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($farm['nom_ferme']) ?></td>
                                        <td><?= htmlspecialchars($farm['localisation']) ?></td>
                                        <td>
                                            <?php 
                                            $count = 0;
                                            foreach ($active_batches as $batch) {
                                                if ($batch['id_ferme'] == $farm['id_ferme']) {
                                                    $count++;
                                                }
                                            }
                                            echo $count;
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= APP_URL ?>/farm/show/<?= $farm['id_ferme'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= APP_URL ?>/farm" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-list me-1"></i>Voir toutes mes fermes
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-clipboard-list me-2"></i>Lots de volailles actifs</h3>
            </div>
            <div class="card-body">
                <?php if (empty($active_batches)): ?>
                    <p class="text-muted">Vous n'avez pas de lots actifs.</p>
                    <?php if (!empty($farms)): ?>
                        <a href="<?= APP_URL ?>/poultryBatch/create" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Ajouter un lot
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Race</th>
                                    <th>Effectif</th>
                                    <th>Date d'arrivée</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_batches as $batch): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($batch['race']) ?></td>
                                        <td><?= htmlspecialchars($batch['effectif_initial']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($batch['date_arrivee'])) ?></td>
                                        <td>
                                            <a href="<?= APP_URL ?>/poultryBatch/view/<?= $batch['id_lot'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= APP_URL ?>/poultryBatch" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-list me-1"></i>Voir tous mes lots
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-syringe me-2"></i>Derniers traitements</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recent_treatments)): ?>
                    <p class="text-muted">Aucun traitement récent.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Lot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_treatments as $treatment): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($treatment['date_application'])) ?></td>
                                        <td><?= htmlspecialchars($treatment['produit']) ?></td>
                                        <td><?= htmlspecialchars($treatment['race']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= APP_URL ?>/treatment" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-list me-1"></i>Voir tous les traitements
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-utensils me-2"></i>Dernières alimentations</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recent_feeds)): ?>
                    <p class="text-muted">Aucune alimentation récente.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                    <th>Lot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_feeds as $feed): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($feed['date_distribution'])) ?></td>
                                        <td><?= htmlspecialchars($feed['type']) ?></td>
                                        <td><?= htmlspecialchars($feed['quantite']) ?> kg</td>
                                        <td><?= htmlspecialchars($feed['race']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= APP_URL ?>/feed" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-list me-1"></i>Voir toutes les alimentations
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($_SESSION['user_role'] === 'veterinaire'): ?>
<!-- TABLEAU DE BORD VÉTÉRINAIRE -->
<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-syringe me-2"></i>Derniers traitements enregistrés</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recent_treatments)): ?>
                    <p class="text-muted">Aucun traitement récent.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Posologie</th>
                                    <th>Lot</th>
                                    <th>Ferme</th>
                                    <th>Éleveur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_treatments as $treatment): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($treatment['date_application'])) ?></td>
                                        <td><?= htmlspecialchars($treatment['produit']) ?></td>
                                        <td><?= htmlspecialchars($treatment['posologie']) ?></td>
                                        <td><?= htmlspecialchars($treatment['race']) ?></td>
                                        <td><?= htmlspecialchars($treatment['nom_ferme']) ?></td>
                                        <td><?= htmlspecialchars($treatment['prenom'] . ' ' . $treatment['nom']) ?></td>
                                        <td>
                                            <a href="<?= APP_URL ?>/treatment/view/<?= $treatment['id_traitement'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= APP_URL ?>/treatment" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-list me-1"></i>Voir tous les traitements
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($_SESSION['user_role'] === 'admin'): ?>
<!-- TABLEAU DE BORD ADMINISTRATEUR -->
<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Utilisateurs</h6>
                        <h2 class="mb-0"><?= $total_users ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/users" class="text-primary d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Fermes</h6>
                        <h2 class="mb-0"><?= $total_farms ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-farm fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/farms" class="text-success d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Lots de volailles</h6>
                        <h2 class="mb-0"><?= $total_batches ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/poultryBatches" class="text-info d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Traitements</h6>
                        <h2 class="mb-0"><?= $total_treatments ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-syringe fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/treatments" class="text-warning d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Alimentations</h6>
                        <h2 class="mb-0"><?= $total_feeds ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-utensils fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/feeds" class="text-danger d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-secondary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Posts forum</h6>
                        <h2 class="mb-0"><?= $total_posts ?></h2>
                    </div>
                    <div>
                        <i class="fas fa-comments fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= APP_URL ?>/admin/forum" class="text-secondary d-flex justify-content-between align-items-center">
                    <span>Voir détails</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- SECTION FORUM COMMUNE À TOUS LES UTILISATEURS -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="h5 mb-0"><i class="fas fa-comments me-2"></i>Dernières discussions du forum</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recent_posts)): ?>
                    <p class="text-muted">Aucune discussion récente.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($recent_posts as $post): ?>
                            <a href="<?= APP_URL ?>/forum/view/<?= $post['id_post'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= htmlspecialchars($post['titre']) ?></h5>
                                    <small><?= date('d/m/Y H:i', strtotime($post['date_post'])) ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars(substr($post['contenu'], 0, 100)) . (strlen($post['contenu']) > 100 ? '...' : '') ?></p>
                                <small>Par <?= htmlspecialchars($post['prenom'] . ' ' . $post['nom']) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= APP_URL ?>/forum" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-list me-1"></i>Voir toutes les discussions
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>