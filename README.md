# 🐔 Avicultat - Application de Gestion Avicole

## 📋 À propos
Avicultat est une application web PHP native conçue pour moderniser la gestion des exploitations avicoles en Guinée. Elle permet aux éleveurs et vétérinaires de gérer efficacement leurs activités quotidiennes, du suivi des lots à la gestion des traitements.

## 🚀 Fonctionnalités principales

- 👥 Gestion des utilisateurs (Éleveurs, Vétérinaires, Administrateurs)
- 🏡 Gestion des fermes
- 🐤 Suivi des lots de volailles
- 💊 Gestion des traitements vétérinaires
- 🌾 Suivi de l'alimentation
- 💬 Forum communautaire
- 📊 Tableau de bord avec statistiques

## 🔧 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache recommandé)
- Extensions PHP requises :
  - PDO
  - PDO_MySQL
  - mbstring
  - json

## ⚙️ Installation

1. **Cloner le projet**
```powershell
git clone [url-du-repo]
cd Avicultat
```

2. **Configuration de la base de données**
- Créer une base de données MySQL
- Importer le fichier de structure : `/sql/aviculture.sql`

3. **Configuration de l'environnement**
- Copier `.env.example` vers `.env`
- Modifier les paramètres dans `.env` :
```ini
DB_HOST=localhost
DB_NAME=nom_de_votre_base
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe
```

4. **Configuration du serveur web**
- Pointer le DocumentRoot vers le dossier `public/`
- S'assurer que le module mod_rewrite est activé
- Le fichier .htaccess est déjà configuré pour la réécriture d'URL

5. **Permissions**
```powershell
# Créer le dossier logs s'il n'existe pas
mkdir -p logs
# Définir les permissions (sous Linux)
chmod 755 logs
```

## 🚀 Déploiement avec WAMP

### 1. Installation préalable

- Télécharger et installer WAMP depuis [wampserver.com](http://www.wampserver.com/)
- Vérifier que WAMP est correctement installé (icône verte dans la barre des tâches)

### 2. Configuration du projet

1. **Copier le projet**
   - Naviguer vers le dossier `C:\wamp64\www\`
   - Créer un dossier `avicultat`
   - Copier tous les fichiers du projet dans ce dossier

2. **Configuration de la base de données**
   - Ouvrir phpMyAdmin ([http://localhost/phpmyadmin](http://localhost/phpmyadmin))
   - Créer une nouvelle base de données nommée `avicultat`
   - Importer le fichier `sql/aviculture.sql`

3. **Configuration du Virtual Host**
   - Clic gauche sur l'icône WAMP → Apache → Virtual Hosts → Gestion Virtual Hosts
   - Ajouter un nouveau virtual host :

   ```apache
   Nom : avicultat.local
   Chemin : C:\wamp64\www\avicultat\public
   ```

   - Redémarrer les services DNS (clic gauche sur l'icône WAMP → Outils → Redémarrage DNS)

4. **Configuration fichier hosts**
   - Ouvrir en administrateur : `C:\Windows\System32\drivers\etc\hosts`
   - Ajouter la ligne :

   ```text
   127.0.0.1 avicultat.local
   ```

5. **Configuration de l'environnement**
   - Copier `.env.example` vers `.env`
   - Modifier avec les paramètres suivants :

   ```ini
   DB_HOST=localhost
   DB_NAME=avicultat
   DB_USER=root
   DB_PASS=
   ```

### 3. Accès à l'application

- Ouvrir votre navigateur
- Accéder à : [http://avicultat.local](http://avicultat.local)
- L'application devrait maintenant être fonctionnelle

### 4. Résolution des problèmes courants

- Si la page affiche une erreur 404 :
  - Vérifier que le module rewrite est activé dans WAMP
  - Clic gauche sur l'icône WAMP → Apache → Modules Apache → rewrite_module

- Si vous avez des erreurs de base de données :
  - Vérifier les identifiants dans le fichier `.env`
  - Vérifier que la base de données a bien été importée

## 🏗️ Structure du projet

```
/avicultat/
├── /app/                    # Logique métier
│   ├── /controllers/       # Contrôleurs
│   ├── /models/           # Modèles
│   └── /views/            # Vues
├── /config/                # Configuration
├── /core/                  # Classes MVC de base
├── /public/                # Point d'entrée public
├── /includes/             # Éléments partagés
└── /sql/                  # Scripts base de données
```

## 🔐 Rôles utilisateurs

### 👨‍🌾 Éleveur
- Gestion complète des fermes
- Suivi des lots de volailles
- Gestion des traitements et de l'alimentation

### 👨‍⚕️ Vétérinaire
- Consultation des traitements
- Participation au forum professionnel
- Gestion du profil

### 👨‍💼 Administrateur
- Gestion des utilisateurs
- Modération du forum
- Accès aux statistiques globales

## 🔒 Sécurité

- Protection contre les injections SQL (PDO)
- Hashage des mots de passe (bcrypt)
- Validation des entrées
- Protection CSRF
- Gestion sécurisée des sessions

## 📝 Développement

### Convention de code
- PSR-4 pour l'autoloading
- Indentation : 4 espaces
- Nommage : PascalCase pour les classes, camelCase pour les méthodes

### Contribution
1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📋 Tests

```powershell
# Lancer le serveur de développement PHP
php -S localhost:8000 -t public/
```

## 📚 Documentation

Documentation complète disponible dans le dossier `/docs`

## 📄 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE.md](LICENSE.md) pour plus de détails

## 🤝 Support

Pour toute question ou problème :
- Consulter la documentation
- Ouvrir une issue sur le projet
- Contacter l'équipe de développement

## ✨ Remerciements

- À tous les contributeurs du projet
- À la communauté des éleveurs de Guinée pour leur feedback
- Aux testeurs et early adopters
