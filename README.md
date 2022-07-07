## Instruction d'installation du projet

### Versions

PHP: 7.3.7
Symfony: 5.4

### Installation sur un serveur local

Pour installer le projet sur un serveur local il faut placer le dossier contenant le code dans le dossier qui contient les autres applications fonctionnant sur le serveur.

Une fois le projet installé sur le serveur il vous suffira d'ouvrir votre requêteur et saisir l'url pointant sur le dossier de votre projet et ajouter /public. Exemple: 127.0.0.1/NomDuDossier/public puis d'y ajouter la route que vous souhaitez requêter(liste des routes dans la documentation).

### Dépendances

Pour installer les bundles nécéssaire au bon fonctionement de l'api, éxecuté la commande suivante au niveau du projet dans votre terminale.

    composer install

### Import de la base de données

Pour récupérer la base de données il faudra importer le fichier 'p7bdd.sql' (à la racine du projet), dans votre système de base de données afin de récupérer les données de démo du projet.

### Modification du code nécessaire

Quelques modifications sont requises pour le bon fonctionnement de l'application.

Dans le fichier '.env' situé à la racine du projet vous devrez modifier l'hôte, le nom d'utilisateur et son mot de passe, le nom  de votre base de données, comme ceci:

    DATABASE_URL="mysql://utilisateurBase:motDePasseBase@127.0.0.1:3306/nomBase"

Le projet est maintenant fonctionnel.

### Identifiants de connexion

Voici des identifiants qui vous permettront de tester l'api(à utiliser dans le corps de la requête api/login_check).

##### nom d'utilisateur: main
##### mot de passe: test

