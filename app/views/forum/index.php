<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Forum Communautaire</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/forum/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Sujet
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

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="<?= BASE_URL ?>/forum" method="GET" class="d-flex">
                        <input type="text" class="form-control me-2" name="search" placeholder="Rechercher un sujet..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="<?= BASE_URL ?>/forum" method="GET" class="d-flex">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">Toutes les catégories</option>
                            <option value="general" <?= ($category ?? '') === 'general' ? 'selected' : '' ?>>Général</option>
                            <option value="sante" <?= ($category ?? '') === 'sante' ? 'selected' : '' ?>>Santé</option>
                            <option value="alimentation" <?= ($category ?? '') === 'alimentation' ? 'selected' : '' ?>>Alimentation</option>
                            <option value="equipement" <?= ($category ?? '') === 'equipement' ? 'selected' : '' ?>>Équipement</option>
                            <option value="conseils" <?= ($category ?? '') === 'conseils' ? 'selected' : '' ?>>Conseils</option>
                        </select>
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Liste des sujets -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Réponses</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Aucun sujet trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>/forum/show/<?= $post['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $this->getCategoryColor($post['category']) ?>">
                                            <?= htmlspecialchars($post['category']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($post['author_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
                                    <td><?= $post['comment_count'] ?></td>
                                    <td>
                                        <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
                                            <a href="<?= BASE_URL ?>/forum/edit/<?= $post['id'] ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?= BASE_URL ?>/forum/delete/<?= $post['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?');">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigation des sujets" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?>">Précédent</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?>">Suivant</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include('partials/footer.php'); ?> 