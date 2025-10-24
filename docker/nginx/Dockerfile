# =========================
# 🌐 Nginx optimisé avec assets statiques
# =========================
# Cette image copie les assets depuis l'image Laravel déjà buildée
# pour éviter le problème de persistence des volumes Docker
ARG GITHUB_REPOSITORY=fredchess/onenation_oneheart
ARG IMAGE_TAG=latest
FROM ghcr.io/${GITHUB_REPOSITORY}:${IMAGE_TAG} AS assets

# Image finale Nginx ultra-légère
FROM nginx:stable-alpine

# Copier la configuration Nginx personnalisée
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# Créer le répertoire pour les fichiers Laravel
RUN mkdir -p /var/www/public

# Copier UNIQUEMENT le dossier public depuis l'image Laravel
# Cela inclut les assets buildés (/var/www/public/build)
COPY --from=assets /var/www/public /var/www/public

# Ajuster les permissions
# Note: On garde les fichiers PHP car Nginx doit pouvoir les lire
# pour les passer à php-fpm (ils ne sont pas exécutés par Nginx)
RUN chown -R nginx:nginx /var/www/public

# Pas d'exposition de port (géré par Traefik dans Dokploy)
CMD ["nginx", "-g", "daemon off;"]