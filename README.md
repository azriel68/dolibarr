Travailler sur le projet
========================

Prérequis: une base de données accessible.

Prérequis:
- docker
- docker-compose
- make (pour faciliter les lignes de commande)

```sh
cd ~/PhpstormProjects
git clone git@repo dolibarr --branch=8.0
cd dolibarr
# setup the .env file
cp .env.dist .env
sed -i 's|\/home\/login|'"${HOME}"'|' .env
# for mac, setup FS_MOUNT=delegated
make build
make up
make init-db
```

Entrer dans le terminal docker:
```sh
make php
```

Lancer la mise à jour:
```sh
make upgrade
```

Accéder à l'application:
http://dolibarr.localhost
login : admin
password : test
