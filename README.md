# 🎣 Campagne de Phishing – Sensibilisation à la Sécurité Informatique

> **⚠️ AVERTISSEMENT LÉGAL**
>
> Ce projet est **exclusivement destiné à la sensibilisation et à la formation en cybersécurité**.
>
> Son déploiement est strictement réservé à des environnements contrôlés, avec le **consentement explicite** des participants et de l'organisation concernée.
>
> Toute utilisation malveillante, non autorisée ou à des fins frauduleuses est **illégale** et passible de poursuites pénales (notamment en vertu des articles 323-1 et suivants du Code pénal français).
>
> Toutes les données collectées dans le cadre de cette démonstration proviennent exclusivement de **comptes de test créés pour l'exercice**. Aucun identifiant réel ne doit être utilisé. Le projet est destiné à des environnements de laboratoire ou de sensibilisation avec consentement préalable des participants.

---

## 📌 Présentation

Ce projet simule une **campagne de phishing réaliste** ciblant un compte Google de démonstration dans un objectif pédagogique.

Il reproduit le parcours d'authentification habituellement rencontré par les utilisateurs afin de montrer comment une attaque d'ingénierie sociale peut conduire à la divulgation d'informations sensibles.

Le scénario se déroule en trois étapes :

1. **Saisie de l'adresse e-mail**
2. **Saisie du mot de passe**
3. **Validation en deux étapes (2FA)**

Chaque étape enregistre les informations saisies par les **comptes de test** dans une base de données MySQL afin d'illustrer les conséquences potentielles d'une attaque de phishing lors des séances de sensibilisation.

---

## 🎯 Objectifs pédagogiques

Ce projet permet d'aborder les notions suivantes :

* Compréhension des mécanismes de phishing
* Sensibilisation aux attaques d'ingénierie sociale
* Analyse du comportement des utilisateurs face à un site frauduleux
* Identification des indicateurs de compromission
* Sensibilisation à l'authentification multifacteur (MFA / 2FA)
* Mise en œuvre des bonnes pratiques de cybersécurité
* Présentation des conséquences d'une fuite d'identifiants

---

## 🗂️ Structure du projet

```text
Phishing/
├── index.php                       # Page 1 – Faux formulaire Google (e-mail)
├── google-password.php             # Page 2 – Mot de passe
├── google-2fa.php                  # Page 3 – Validation 2FA
├── google-home.html                # Landing page
├── google-logo-2.png               # Ressource graphique
├── unnamed.png                     # Ressource graphique complémentaire
└── Campagne de Phishing Google.pdf # Documentation du scénario pédagogique
```

---

## ⚙️ Prérequis

* PHP 7.4 ou supérieur
* MySQL 5.7+ ou MariaDB
* Apache ou Nginx
* Environnement de laboratoire ou de démonstration

Exemples :

* XAMPP
* WAMP
* Laragon
* Laravel Herd
* Serveur VPS de test

---

## 🗄️ Configuration de la base de données

Créez les bases et tables suivantes avant le déploiement :

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

Dans chacun des fichiers PHP :

* `index.php`
* `google-password.php`
* `google-2fa.php`

modifiez les paramètres de connexion MySQL :

```php
$host     = '127.0.0.1';
$username = 'votre_utilisateur';
$password = 'votre_mot_de_passe';
```

---

## 🚀 Déploiement

1. Clonez le dépôt Git.
2. Placez les fichiers dans votre répertoire web.
3. Créez les bases de données et les tables nécessaires.
4. Configurez les accès MySQL.
5. Vérifiez le fonctionnement local.
6. Accédez à la page d'accueil :

```text
http://votre-domaine/
```

Le point d'entrée de l'application est :

```text
index.php
```

---

## 🔄 Flux utilisateur simulé

```text
index.php
    │
    ├── Saisie de l'adresse e-mail
    ▼
google-password.php
    │
    ├── Saisie du mot de passe
    ▼
google-2fa.php
    │
    ├── Saisie du code 2FA
    ▼
Fin du scénario pédagogique
```

### Flux de données

```text
index.php
    │
    ▼
tracking_db
(login_attempts)

google-password.php
    │
    ▼
password_db
(single_passwords)

google-2fa.php
    │
    ▼
site_data
(verification_codes)
```

---

## 🎓 Utilisation pédagogique recommandée

1. Déployer le projet sur un environnement de laboratoire.
2. Informer les participants du cadre pédagogique de l'exercice.
3. Envoyer un scénario de phishing contrôlé.
4. Observer les différentes étapes franchies par les participants.
5. Réaliser un débriefing collectif.
6. Présenter les bonnes pratiques permettant d'éviter ce type d'attaque.

---

## 🔐 Bonnes pratiques de sécurité à enseigner

| Indice de phishing   | Élément à identifier                               |
| -------------------- | -------------------------------------------------- |
| URL du domaine       | Différente du domaine officiel                     |
| Certificat SSL       | Invalide, absent ou suspect                        |
| Expéditeur           | Domaine incohérent ou usurpé                       |
| Demande inhabituelle | Action sensible demandée de manière inattendue     |
| Code 2FA             | Ne jamais communiquer un code reçu                 |
| Urgence artificielle | Tentative de pression psychologique                |
| Fautes de langue     | Erreurs fréquentes dans les campagnes frauduleuses |

---

## 📊 Exploitation des résultats

Les résultats collectés peuvent être utilisés pour :

* Mesurer le taux de clic sur un lien frauduleux
* Évaluer le niveau de sensibilisation des participants
* Identifier les points de vigilance
* Adapter les futures campagnes de sensibilisation
* Construire un plan d'amélioration de la sécurité

---

## ⚠️ Limites du projet

Ce projet est une démonstration pédagogique.

Il ne doit pas être utilisé :

* pour collecter des identifiants réels ;
* contre des personnes non informées ;
* sur des systèmes de production ;
* dans un contexte non autorisé.

L'auteur décline toute responsabilité en cas d'utilisation contraire aux objectifs pédagogiques décrits dans cette documentation.

---

## 📄 Licence

Ce projet est distribué à des fins **éducatives et de sensibilisation uniquement**.

Toute redistribution ou réutilisation doit conserver les mentions relatives à l'objectif pédagogique du projet ainsi que le présent avertissement.

---

## 👤 Auteur

Projet réalisé dans le cadre d'une campagne de sensibilisation à la cybersécurité visant à démontrer les mécanismes du phishing et les bonnes pratiques permettant de s'en protéger.
