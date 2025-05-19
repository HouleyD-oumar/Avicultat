/aviculture-app/
│
├── /app/                      # Contient le code métier
│   ├── /controllers/          # Contrôleurs (logique)
│   │   └── FarmController.php
│   ├── /models/               # Modèles (accès BD)
│   │   └── Farm.php
│   └── /views/                # Vues (HTML)
│       └── farms/
│           └── index.php
│
├── /config/                   # Fichier de configuration DB
│   └── config.php
│
├── /core/                     # Classes de base MVC
│   ├── Controller.php         # Classe mère des contrôleurs
│   ├── Model.php              # Classe mère des modèles
│   └── App.php                # Routeur principal (dispatcher)
│
├── /public/                   # Point d’entrée accessible via le navigateur
│   ├── /css/
│   ├── /js/
│   └── index.php              # Point d’entrée unique (Front Controller)
│
├── /includes/                 # Fichiers partagés (header, footer)
│   ├── header.php
│   └── footer.php
│
├── /sql/                      # Script de création de la base de données
│   └── aviculture.sql
│
├── .htaccess                  # Redirection vers public/index.php
├── .env                       # Variables d’environnement locales (DB, etc.)
└── README.md                  # Documentation de ton projet
