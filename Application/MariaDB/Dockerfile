FROM mariadb:latest

ARG USER_ID=1000
ARG GROUP_ID=1000

# Asigna grupo y usuario en contenedor para no tener que estar cambiando propietario a los archivos creados desde el contenedor
RUN addgroup --gid ${GROUP_ID} mario
RUN adduser --disabled-password --gecos '' --uid ${USER_ID} --gid ${GROUP_ID} mario

USER 1000

# Set working directory
WORKDIR /var/lib/mysql
