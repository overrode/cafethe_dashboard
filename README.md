# CafThé - Dashboard Vendeur

Projet pédagogique réalisé pour La Fabrique Numérique 41.

L’objectif est de développer un dashboard vendeur pour une entreprise fictive nommée CafThé, spécialisée dans la vente de cafés, thés et accessoires.

## Technologies utilisées

- PHP 8.3 orienté objet
- MySQL 8
- Docker
- phpMyAdmin
- HTML / CSS
- Architecture MVC simple
- Routage simple par query string

## Fonctionnalités réalisées

### Authentification

- Connexion par email et mot de passe
- Mot de passe hashé avec bcrypt
- Déconnexion
- Protection des routes par session
- Rôles utilisateur : administrateur / vendeur

### Dashboard

- Nombre de ventes
- Chiffre d'affaires TTC
- Panier moyen TTC
- Nombre de clients
- Nombre de produits actifs
- Produits en stock faible

### Gestion des produits

- Liste des produits
- Ajout d’un produit
- Modification d’un produit
- Désactivation d’un produit
- Gestion du stock
- Gestion du prix HT et du taux de TVA
- Liaison avec les catégories

### Gestion des clients

- Liste des clients
- Ajout d’un client
- Modification d’un client
- Champs favoris et panier abandonné

### Gestion des ventes

- Création d’une vente en magasin
- Sélection d’un client
- Sélection d’un produit
- Saisie de la quantité
- Calcul automatique HT / TVA / TTC
- Enregistrement dans `sales`
- Enregistrement dans `sale_items`
- Mise à jour automatique du stock

### Gestion des utilisateurs

- Accessible uniquement à l’administrateur
- Liste des utilisateurs
- Ajout d’un utilisateur
- Modification d’un utilisateur
- Désactivation d’un utilisateur

## Structure du projet

```text
app/
├── Controllers/
├── Core/
├── Models/
└── Views/

config/
database/
docs/
public/