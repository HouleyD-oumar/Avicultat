<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0"><i class="fas fa-sign-in-alt me-2"></i>Connexion</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= APP_URL ?>/user/processLogin" method="POST">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($input['email']) ? htmlspecialchars($input['email']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Pas encore de compte ? <a href="<?= APP_URL ?>/user/register">S'inscrire</a></p>
            </div>
        </div>
    </div>
</div>