<?php include('partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('partials/sidebar.php'); ?>
        
        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= htmlspecialchars($post['title']) ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/forum" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au forum
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

            <!-- Sujet principal -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-<?= $this->getCategoryColor($post['category']) ?>">
                            <?= htmlspecialchars($post['category']) ?>
                        </span>
                        <small class="text-muted ms-2">
                            Posté par <?= htmlspecialchars($post['author_name']) ?> le 
                            <?= date('d/m/Y à H:i', strtotime($post['created_at'])) ?>
                        </small>
                    </div>
                    <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
                        <div>
                            <a href="<?= BASE_URL ?>/forum/edit/<?= $post['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="<?= BASE_URL ?>/forum/delete/<?= $post['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ?');">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="post-content">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                </div>
            </div>

            <!-- Commentaires -->
            <h3 class="mb-3">Commentaires (<?= count($comments) ?>)</h3>
            
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                            <small class="text-muted ms-2">
                                le <?= date('d/m/Y à H:i', strtotime($comment['created_at'])) ?>
                            </small>
                        </div>
                        <?php if ($comment['user_id'] === $_SESSION['user_id']): ?>
                            <form action="<?= BASE_URL ?>/forum/deleteComment/<?= $post['id'] ?>/<?= $comment['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Formulaire de commentaire -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Ajouter un commentaire</h4>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/forum/comment/<?= $post['id'] ?>" method="POST">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Publier
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include('partials/footer.php'); ?> 