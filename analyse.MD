--

# 🧩 Modélisation UML & MCD — Application de Gestion des Aviculteurs

---

## 1. 🔶 **Modèle Conceptuel de Données (MCD)**

Voici les principales entités et leurs relations (notation classique Merise simplifiée) :

### **Entités**

- **Utilisateur (User)**
  - id_user (PK)
  - nom, prénom
  - email
  - mot_de_passe
  - rôle (éleveur, vétérinaire, admin)
  - photo_profil

- **Ferme (Farm)**
  - id_ferme (PK)
  - nom_ferme
  - localisation
  - date_creation
  - id_user (FK)

- **Lot de Volailles (PoultryBatch)**
  - id_lot (PK)
  - race
  - effectif_initial
  - date_arrivee
  - statut (actif, vendu, perte totale)
  - id_ferme (FK)

- **Traitement (Treatment)**
  - id_traitement (PK)
  - produit
  - posologie
  - date_application
  - observations
  - id_lot (FK)

- **Alimentation (Feed)**
  - id_feed (PK)
  - type
  - quantité
  - date_distribution
  - id_lot (FK)

- **Sujet Forum (ForumPost)**
  - id_post (PK)
  - titre
  - contenu
  - date_post
  - id_user (FK)

- **Réponse Forum (ForumReply)**
  - id_reply (PK)
  - contenu
  - date_reply
  - id_post (FK)
  - id_user (FK)

---

### **Relations clés**

- Un **Utilisateur** possède plusieurs **Fermes**
- Une **Ferme** contient plusieurs **Lots**
- Un **Lot** peut avoir plusieurs **Traitements** et **Alimentations**
- Un **Utilisateur** peut créer plusieurs **ForumPosts** et **ForumReplies**

---

## 2. 📘 Diagramme de Classes UML (structuration logique)

```plaintext
+------------------+
|     Utilisateur  |
+------------------+
| - id_user        |
| - nom            |
| - prénom         |
| - email          |
| - mot_de_passe   |
| - rôle           |
| - photo_profil   |
+------------------+
        |
        | 1
        |-------< possède
        | n
+------------------+
|      Ferme       |
+------------------+
| - id_ferme       |
| - nom_ferme      |
| - localisation   |
| - date_creation  |
| - id_user (FK)   |
+------------------+
        |
        | 1
        |-------< contient
        | n
+-----------------------------+
|     LotDeVolailles          |
+-----------------------------+
| - id_lot                    |
| - race                      |
| - effectif_initial          |
| - date_arrivee             |
| - statut                    |
| - id_ferme (FK)             |
+-----------------------------+
    |               |
    | 1             | 1
    |               |
    | n             | n
    V               V
+-------------+   +-------------+
| Traitement  |   | Alimentation|
+-------------+   +-------------+
| - id        |   | - id        |
| - produit   |   | - type      |
| - posologie |   | - quantité  |
| - date      |   | - date      |
| - obs       |   | - id_lot FK |
| - id_lot FK |   +-------------+
+-------------+

+---------------------+
|    ForumPost        |
+---------------------+
| - id_post           |
| - titre             |
| - contenu           |
| - date_post         |
| - id_user (FK)      |
+---------------------+
        |
        | 1
        |-------< a
        | n
+----------------------+
|   ForumReply         |
+----------------------+
| - id_reply           |
| - contenu            |
| - date_reply         |
| - id_post (FK)       |
| - id_user (FK)       |
+----------------------+
```

---

## 3. 🧭 Diagramme de Cas d’Utilisation (Use Case)

### Acteurs :
- Éleveur
- Vétérinaire
- Administrateur

```plaintext
          [ Éleveur ]
               |
        +----------------------+
        |  Gérer ses fermes    |
        +----------------------+
               |
        +-----------------------------+
        |  Suivre lots de volailles   |
        +-----------------------------+
               |
        +-----------------------------+
        |  Enregistrer alimentation   |
        +-----------------------------+
               |
        +-----------------------------+
        |  Enregistrer traitement     |
        +-----------------------------+
               |
        +-----------------------------+
        |  Poster dans le forum       |
        +-----------------------------+

          [ Vétérinaire ]
               |
        +-----------------------------+
        |  Consulter traitements      |
        +-----------------------------+
        |  Participer au forum        |
        +-----------------------------+

          [ Administrateur ]
               |
        +-----------------------------+
        |  Gérer utilisateurs         |
        +-----------------------------+
        |  Modérer forum              |
        +-----------------------------+
        |  Voir statistiques système  |
        +-----------------------------+
```

