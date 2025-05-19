<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <i class="fas fa-exclamation-circle text-danger fa-4x mb-4"></i>
                        <h1 class="display-4 mb-4">404</h1>
                        <h2 class="h4 mb-4">Page non trouvée</h2>
                        <p class="text-muted mb-4">La page que vous recherchez n'existe pas ou a été déplacée.</p>
                        <a href="<?= APP_URL ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 