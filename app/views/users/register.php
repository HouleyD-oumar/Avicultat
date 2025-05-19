<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0"><i class="fas fa-user-plus me-2"></i>Inscription</h2>
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
                
                <form action="<?= APP_URL ?>/user/processRegister" method="POST">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= isset($input['nom']) ? htmlspecialchars($input['nom']) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= isset($input['prenom']) ? htmlspecialchars($input['prenom']) : '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($input['email']) ? htmlspecialchars($input['email']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="eleveur" <?= (isset($input['role']) && $input['role'] === 'eleveur') ? 'selected' : '' ?>>Éleveur</option>
                            <option value="veterinaire" <?= (isset($input['role']) && $input['role'] === 'veterinaire') ? 'selected' : '' ?>>Vétérinaire</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Déjà inscrit ? <a href="<?= APP_URL ?>/user/login">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>