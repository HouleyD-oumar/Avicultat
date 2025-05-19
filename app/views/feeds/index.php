<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestion de l'Alimentation</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/feeds/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Entrée
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lot</th>
                            <th>Type d'Aliment</th>
                            <th>Quantité (kg)</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feeds as $feed): ?>
                            <tr>
                                <td><?= htmlspecialchars($feed['id']) ?></td>
                                <td><?= htmlspecialchars($feed['batch_name']) ?></td>
                                <td><?= htmlspecialchars($feed['feed_type']) ?></td>
                                <td><?= htmlspecialchars($feed['quantity']) ?></td>
                                <td><?= date('d/m/Y', strtotime($feed['feeding_date'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/feeds/show/<?= $feed['id'] ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/feeds/edit/<?= $feed['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/feeds/delete/<?= $feed['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php include('partials/footer.php'); ?> 