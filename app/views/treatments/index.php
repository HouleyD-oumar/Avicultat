<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestion des Traitements</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/treatments/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Traitement
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
                            <th>Type de Traitement</th>
                            <th>Date</th>
                            <th>Dosage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($treatments as $treatment): ?>
                            <tr>
                                <td><?= htmlspecialchars($treatment['id']) ?></td>
                                <td><?= htmlspecialchars($treatment['batch_name']) ?></td>
                                <td><?= htmlspecialchars($treatment['treatment_type']) ?></td>
                                <td><?= date('d/m/Y', strtotime($treatment['treatment_date'])) ?></td>
                                <td><?= htmlspecialchars($treatment['dosage']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/treatments/show/<?= $treatment['id'] ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/treatments/edit/<?= $treatment['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/treatments/delete/<?= $treatment['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce traitement ?');">
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