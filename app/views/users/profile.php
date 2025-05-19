<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0"><i class="fas fa-user-circle me-2"></i>Mon Profil</h2>
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
                
                <form action="<?= APP_URL ?>/user/updateProfile" method="POST" enctype="multipart/form-data">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <?php if (!empty($user['photo_profil'])): ?>
                                <img src="<?= APP_URL ?>/uploads/profiles/<?= htmlspecialchars($user['photo_profil']) ?>" alt="Photo de profil" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="photo_profil" class="form-label">Changer la photo</label>
                                <input type="file" class="form-control" id="photo_profil" name="photo_profil">
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Rôle</label>
                                <input type="text" class="form-control" value="<?= ucfirst(htmlspecialchars($user['role'])) ?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4 class="mb-3">Changer le mot de passe</h4>
                    <p class="text-muted mb-3">Laissez vide si vous ne souhaitez pas modifier votre mot de passe.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                            <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>