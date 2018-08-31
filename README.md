Dolibarr SFAM
============

Dolibarr de la société SFAM

Travailler sur le projet
========================

Prérequis: une base de données accessible.

Prérequis:
- docker
- docker-compose
- make (pour faciliter les lignes de commande)

```sh
cd ~/PhpstormProjects
git clone git@gitlab.sfam.ovh:sfk/docker/dolibarr.git dolibarr --branch=8.0
cd dolibarr
# setup the .env file
cp .env.dist .env
sed -i 's|\/home\/login|'"${HOME}"'|' .env
# for mac, setup FS_MOUNT=delegated
wget -O .git/hooks/pre-commit https://conf.sfam.ovh/workstation/default/home/user/git/hooks/pre-commit && chmod +x .git/hooks/pre-commit
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
