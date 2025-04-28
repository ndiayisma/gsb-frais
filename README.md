# GSB Version Symfony

## Petite Histoire

GSB, Galaxy Swiss Bourdin, est un laboratoire issue d'une fusion entre le géant américain Galaxy (spécialisé dans le secteur des maladies virales dont le SIDA et les hépatites) et le conglomérat européen Swiss Bourdin (travaillant sur des médicaments plus conventionnels), lui-même déjà union de trois petits laboratoires.
En 2009, les deux géants pharmaceutiques ont uni leurs forces pour créer un leader de ce secteur industriel. L’entité Galaxy Swiss Bourdin Europe a établi son siège administratif à Paris.
Le siège social de la multinationale est situé à Philadelphie, Pennsylvanie, aux Etats-Unis.

## 💊Contexte

Dans le cadre d'un projet de développement débutant de septembre 2024 à Avril 2025, le but est de réaliser un application web qui permettra aux visiteurs de consulter et suivre leur fiche de frais afin que les comptables puissent les valider une fois le mois en cours passée

## 🪛Configuration Système recommandé

- Windows 10/11
- macOS Ventura (Version 13) ou ultérieure
- Version Linux datant d'après 2022
- **16 Go** de Stockage HDD minumum
- **8 Go** de RAM minimum

## Architecture utilisés

- PhP 8.3 (utilisé pour le BackEnd)
- HTML 5(peu utilisé pour la Vue)
- Symfony Flex (utilisé pour faciliter l'usage de l'Architecture MVC **(Modèle, Vue, Controller)**
- MySQL (utilisation de la base de données
- Tailwind CSS (framework pour le développement web, surtout utilisé pour le responsive)

## 🗃️Arborescence utilisé
```
src/
├── Controller/
├── Entity/
├── Form/
├── Repository/
├── Security/
templates/
```

## 😼 Git

Git est un logiciel de gestion de versions décentralisé. C'est un logiciel libre et gratuit, créé en 2005 par Linus Torvalds, auteur du noyau Linux, et distribué selon les termes de la licence publique
générale GNU version 2.

## Déploiement du répositoire Git

1. Cloner le dépôt du répositoire grâce a cette commande dans votre terminal
```bash
git clone https://github.com/ndiayisma/gsb-frais.git
```

2. Installer les dépendances nécessaire avec le bundle de composer
```bash
composer install
```

3. Configure votre .env ou créer .env.local pour vous connecter à votre base de données
4. Créer et mettre à jour la base :
```bash
php bin/console doctrine:database:create
```
Puis
```bash
php bin/console doctrine:migrations:migrate
```
Ou si la migration n'est pas passée, assurez-vous de le mettre fréquemment à jour et après avoir ajouté une attribut d'une entity et après avoir fait les imports
```bash
symfony console doctrine:schema:update --force --complete
```
5. Ainsi, vous pouvez lancer votre application
```bash
symfony serve -d
```

Pour faire plus rapide

Lien : https://github.com/ndiayisma/gsb-frais.git


## Exemples

Ici le comptable choisit le visiteur et la fiche du mois afin de le confirmer et vérifier si une Ligne hors Forfait doit être REFUSE
![image](https://github.com/user-attachments/assets/9ede362a-22e2-4348-8e91-0c837a3d6be3)
![image](https://github.com/user-attachments/assets/66f81455-06ef-49fe-8e75-11cdf380e6ae)

Chaque mois la saisie est cliqué, si un visiteur n'a pas de fiche, la fiche se crée et est enregistrée sur la base de données, et le visiteur peut modifier, ajouter la fiche frais et hors forfait qu'il le souhaite

![image](https://github.com/user-attachments/assets/a9c5e742-c716-4203-8046-ab22858c754e)

