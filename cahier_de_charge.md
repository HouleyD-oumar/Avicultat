

# 📘 Cahier des Charges Fonctionnel  
## Application Web de Gestion Avicole  
**Technologie : PHP natif – MySQL – HTML/CSS/JS**

---

## 1. 🔷 Présentation Générale

### 1.1 Contexte
L’aviculture représente une activité agricole essentielle en Guinée, mais elle souffre encore d’une gestion traditionnelle (papier ou orale). Ce manque de digitalisation entraîne une mauvaise traçabilité, des pertes de données et une faible efficacité. L’objectif du projet est de concevoir une application web simple, légère et accessible, permettant aux éleveurs et vétérinaires de gérer efficacement leur activité avicole.

### 1.2 Objectifs
- Faciliter la gestion des fermes, lots, traitements et alimentation.
- Assurer une traçabilité des opérations réalisées.
- Créer une plateforme communautaire d’échange entre professionnels.
- Proposer une interface claire, responsive et multirôle.

---

## 2. 🔶 Description des Utilisateurs

| Rôle              | Accès et droits                                                                                                                                  |
|-------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| **Éleveur**        | Création et gestion des fermes, suivi des lots de volailles, saisie des traitements et de l’alimentation, interaction dans le forum.          |
| **Vétérinaire**    | Consultation des traitements, réponse aux sujets du forum, profil personnel.                                                                   |
| **Administrateur** | Gestion et supervision de tous les utilisateurs, modération du forum, accès au tableau de bord global (statistiques, alertes, etc.).           |

---

## 3. 📌 Fonctionnalités Détailées

### 3.1 Gestion des comptes
- Création de compte avec rôle sélectionné.
- Connexion / déconnexion sécurisée (session PHP).
- Profil utilisateur (modification des informations personnelles).

### 3.2 Gestion des fermes
- Ajouter une ferme (nom, localisation, superficie).
- Modifier / Supprimer une ferme liée à un utilisateur.
- Visualiser la liste de ses propres fermes.

### 3.3 Gestion des lots de volailles
- Ajout de lots (espèce, nombre, date d’introduction, origine).
- Suivi du cycle de vie : croissance, vente, mortalité.
- Lier un lot à une ferme.

### 3.4 Gestion des traitements vétérinaires
- Saisie d’un traitement (date, type, produit, vétérinaire).
- Liste des traitements par lot.
- Historique consultable par éleveur et vétérinaire.

### 3.5 Gestion de l’alimentation
- Enregistrement des rations distribuées.
- Suivi des types et quantités d’aliments consommés.
- Alertes en cas de rupture estimée.

### 3.6 Forum communautaire
- Création de sujets de discussion.
- Réponses aux sujets (fil de discussion).
- Système de signalement/modération.

### 3.7 Tableau de bord (éleveur / admin)
- Résumé visuel : nombre de fermes, lots actifs, alertes (mortalité, traitements à venir, etc.).
- Graphiques basiques : évolution des effectifs, fréquence des traitements.

---

## 4. 🏗️ Architecture Technique

### 4.1 Technologies

| Couche     | Technologie utilisée                       |
|------------|---------------------------------------------|
| Frontend   | HTML5, CSS3 (Bootstrap si souhaité), JS     |
| Backend    | PHP natif organisé en structure MVC simple  |
| Base de données | MySQL (relationnelle, normalisée)          |

### 4.2 Structure des fichiers
```
/app
  /controllers
  /models
  /views
/public
  /css
  /js
/includes
/index.php
/config.php
/.env
```

### 4.3 Sécurité
- Protection contre injections SQL (requêtes préparées via PDO).
- Hashage des mots de passe (bcrypt).
- Vérification côté serveur & client des entrées.
- Système de session pour la gestion des connexions.
- Vérification CSRF sur les formulaires sensibles.

---

## 5. 🗃️ Base de Données – Tables principales

| Table             | Description |
|-------------------|-------------|
| `users`           | Utilisateurs (nom, email, rôle, mot de passe) |
| `farms`           | Fermes gérées par un utilisateur |
| `poultry_lots`    | Lots de volailles |
| `treatments`      | Traitements administrés |
| `feeds`           | Enregistrements alimentaires |
| `forum_topics`    | Sujets du forum |
| `forum_replies`   | Réponses aux sujets |
| `alerts`          | Alertes diverses (mortalité, rupture, etc.) |

---

## 6. 📆 Planning Prévisionnel

| Phase                      | Durée estimée |
|---------------------------|----------------|
| Analyse & MCD/MLD         | 3 jours        |
| Conception interface (HTML/CSS) | 3 jours  |
| Développement backend     | 6 à 8 jours    |
| Intégration et tests      | 3 jours        |
| Documentation & livraison | 2 jours        |

---

## 7. 📦 Livrables attendus

- Code source PHP complet (bien organisé, commenté).
- Base de données MySQL exportée (.sql).
- Diagrammes MCD, MLD, MPD, UML (cas d’utilisation, classes).
- Manuel utilisateur (PDF ou HTML).
- Rapport de projet incluant :
  - Introduction
  - Objectifs
  - Analyse fonctionnelle
  - Conception
  - Captures d’écran
  - Difficultés rencontrées

---

## 8. ✅ Contraintes

- Ne pas utiliser de frameworks PHP (Laravel, Symfony, etc.).
- L’application doit fonctionner en local (localhost via XAMPP, WAMP).
- Être responsive (Bootstrap ou media queries).
- Présentation académique claire et professionnelle.

---
