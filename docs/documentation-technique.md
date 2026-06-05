# Documentation technique - CafThé Dashboard Vendeur

## 1. Objectif du projet

Le projet CafThé Dashboard Vendeur est une application web back-office permettant à des vendeurs et administrateurs de gérer l’activité commerciale d’une boutique fictive spécialisée dans le café, le thé et les accessoires.

Le périmètre réalisé concerne uniquement le dashboard vendeur.

## 2. Stack technique

- PHP 8.3 orienté objet
- MySQL 8
- Docker
- Apache
- phpMyAdmin
- HTML / CSS
- PDO pour les accès base de données

## 3. Architecture

Le projet utilise une architecture MVC simple.

### Core

Le dossier `app/Core` contient les classes techniques communes :

- `Router` : gère le routage de l’application
- `Controller` : classe parente des contrôleurs
- `Database` : connexion PDO à MySQL
- `Auth` : helper pour l’utilisateur connecté

### Models

Le dossier `app/Models` contient les classes responsables des requêtes SQL :

- `Product`
- `Client`
- `Sale`
- `Dashboard`
- `User`

### Controllers

Le dossier `app/Controllers` contient la logique des pages :

- `ProductController`
- `ClientController`
- `SaleController`
- `DashboardController`
- `AuthController`
- `UserController`

### Views

Le dossier `app/Views` contient les pages HTML/PHP.

Un layout partagé est utilisé :

- `layout/header.php`
- `layout/footer.php`

## 4. Base de données

La base de données s’appelle `cafethe`.

Tables principales :

- `users`
- `categories`
- `products`
- `clients`
- `sales`
- `sale_items`

## 5. Authentification

L’authentification fonctionne avec une session PHP.

Le mot de passe est hashé avec `password_hash()` et vérifié avec `password_verify()`.

Les routes sont protégées dans `public/index.php`.

La route `/login` est publique. Toutes les autres routes nécessitent un utilisateur connecté.

## 6. Gestion des rôles

Deux rôles existent :

- `admin`
- `vendeur`

L’administrateur peut gérer les utilisateurs.

Le vendeur peut utiliser le dashboard, les produits, les clients et les ventes.

## 7. Produits

Les produits sont liés à une catégorie.

Un produit contient :

- SKU
- nom
- description
- type de vente
- prix HT
- taux TVA
- stock
- image
- origine
- statut actif/inactif

La suppression est remplacée par une désactivation avec le champ `is_active`.

## 8. Clients

Le module client permet :

- lister les clients
- ajouter un client
- modifier un client

Les champs `favorites` et `abandoned_cart` sont conservés en texte simple pour garder une version compacte du projet.

## 9. Ventes

La version actuelle permet une vente avec un seul produit.

Lors d’une vente :

1. Le vendeur sélectionne un client.
2. Le vendeur sélectionne un produit.
3. Le vendeur entre une quantité.
4. L’application calcule HT, TVA et TTC.
5. La vente est enregistrée dans `sales`.
6. La ligne produit est enregistrée dans `sale_items`.
7. Le stock du produit est diminué.

Une transaction SQL est utilisée pour éviter une vente partiellement enregistrée.

## 10. Dashboard

Le dashboard affiche :

- nombre de ventes
- chiffre d’affaires TTC
- panier moyen TTC
- nombre de clients
- nombre de produits actifs
- produits en stock faible

## 11. Choix de routage

Le projet utilise un routage par query string :

```text
/public/index.php?route=/products