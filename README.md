# 🎣 Campagne de Phishing – Sensibilisation à la Sécurité Informatique

> **⚠️ AVERTISSEMENT LÉGAL**
> Ce projet est **exclusivement destiné à la sensibilisation et à la formation en cybersécurité**.
> Son déploiement est strictement réservé à des environnements contrôlés, avec le **consentement explicite** des participants et de l'organisation concernée.
> Toute utilisation malveillante, non autorisée ou à des fins frauduleuses est **illégale** et passible de poursuites pénales (notamment en vertu de la loi française sur la fraude informatique, article 323-1 et suivants du Code pénal).
> Toutes les données collectées dans le cadre de cette démonstration proviennent exclusivement de comptes de test créés pour l'exercice. Aucun identifiant réel ne doit être utilisé. Le projet est destiné à des environnements de laboratoire ou de sensibilisation avec consentement préalable des participants.

---

## 📌 Présentation

Ce projet simule une **campagne de phishing réaliste** ciblant les comptes Google, dans un objectif pédagogique.
Il reproduit fidèlement le parcours d'authentification Google en trois étapes :

1. **Saisie de l'adresse e-mail** (page de connexion)
2. **Saisie du mot de passe**
3. **Validation en deux étapes** (code 2FA)

Chaque étape enregistre les informations saisies par les comptes de test dans une base de données MySQL afin d'illustrer les conséquences potentielles d'une attaque de phishing lors des séances de sensibilisation.

---

## 🗂️ Structure du projet

```
Phishing/
├── index.php              # Page 1 – Faux formulaire de connexion Google (e-mail)
├── google-password.php    # Page 2 – Formulaire de mot de passe
├── google-2fa.php         # Page 3 – Fausse validation 2FA (code à 6 chiffres)
├── google-home.html       # Page d'atterrissage / landing page
├── google-logo-2.png      # Logo Google utilisé dans l'interface                     
└── unnamed.png            # Ressource graphique complémentaire
└── Campagne de Phishing Google.pdf  # Campagne de Phishing cas concret 
```

---
## 🎯 Objectifs pédagogiques

Ce projet permet d'aborder les notions suivantes :

- Compréhension des mécanismes de phishing
- Sensibilisation aux attaques d'ingénierie sociale
- Analyse du comportement des utilisateurs face à un site frauduleux
- Identification des indicateurs de compromission
- Mise en œuvre des bonnes pratiques de cybersécurité
## ⚙️ Prérequis
---

- PHP 7.4+
- MySQL 5.7+ (ou MariaDB)
- Serveur web Apache ou Nginx (ex : XAMPP, WAMP, Laravel Herd, serveur VPS)

---

## 🗄️ Configuration de la base de données

Créez les bases et tables nécessaires avant le déploiement :

```sql
-- Base 1 : enregistrement des e-mails
CREATE DATABASE tracking_db;
USE tracking_db;
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Base 2 : enregistrement des mots de passe
CREATE DATABASE password_db;
USE password_db;
CREATE TABLE single_passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Base 3 : enregistrement des codes 2FA
CREATE DATABASE site_data;
USE site_data;
CREATE TABLE verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code CHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔧 Configuration

Dans chacun des fichiers PHP (`index.php`, `google-password.php`, `google-2fa.php`), modifiez les identifiants de connexion MySQL :

```php
$host     = '127.0.0.1';
$username = 'votre_utilisateur';
$password = 'votre_mot_de_passe';
```

---

## 🚀 Déploiement

1. Clonez ou déposez les fichiers dans le répertoire web de votre serveur (ex : `htdocs/`, `www/`, ou racine d'un virtualhost).
2. Créez les bases de données via le script SQL ci-dessus.
3. Mettez à jour les identifiants de BDD dans les fichiers PHP.
4. Accédez à `http://votre-domaine/` — la page `index.php` est le point d'entrée.

---

## 🔄 Flux utilisateur simulé

```
index.php  ──(email saisi)──▶  google-password.php  ──(mot de passe saisi)──▶  google-2fa.php
   │                                    │                                              │
   ▼                                    ▼                                              ▼
tracking_db                       password_db                                      site_data
(login_attempts)               (single_passwords)                          (verification_codes)
```

---

## 🎓 Utilisation pédagogique recommandée

1. **Déployer** le projet sur un serveur interne ou un domaine dédié à la formation.
2. **Envoyer** un faux e-mail de phishing aux participants (avec leur accord préalable).
3. **Observer** les données collectées pour identifier combien de personnes ont saisi leurs identifiants.
4. **Organiser** une séance de débriefing pour expliquer les indices visuels permettant de détecter un phishing (URL suspecte, certificat SSL, demande inhabituelle, etc.).
5. **Sensibiliser** aux bonnes pratiques : vérification de l'URL, utilisation d'un gestionnaire de mots de passe, méfiance envers les e-mails urgents.

---

## 🔐 Bonne pratiques de sécurité à enseigner

| Indice de phishing | Ce que les utilisateurs auraient dû remarquer |
|--------------------|-----------------------------------------------|
| URL du domaine | Différente de `accounts.google.com` |
| Certificat SSL | Absent ou auto-signé |
| Expéditeur de l'e-mail | Domaine frauduleux ou usurpé |
| Demande de 2FA | Google ne demande jamais le code reçu par SMS sur une page web inconnue |
| Urgence artificielle | Toujours un signal d'alerte |

---

## 📄 Licence

Ce projet est distribué à des fins **éducatives uniquement**.
Toute redistribution ou utilisation doit mentionner explicitement son caractère pédagogique.

---

## 👤 Auteur

Projet réalisé dans le cadre d'une campagne de sensibilisation à la cybersécurité.
