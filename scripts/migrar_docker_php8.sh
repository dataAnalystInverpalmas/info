#!/usr/bin/env bash
# ==============================================================================
# Script de reparación definitiva de php_web (9258) y python_api (8000)
# Ejecutar en el servidor Linux (172.10.18.128) dentro de /proyectos:
#   sudo bash ./info/scripts/migrar_docker_php8.sh
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
INFO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
ROOT_DIR="$(cd "$INFO_DIR/.." && pwd)"
LOG_FILE="$ROOT_DIR/migration.log"

rm -f "$LOG_FILE"
touch "$LOG_FILE"
chmod 666 "$LOG_FILE"
exec > >(tee -a "$LOG_FILE") 2>&1

echo "=== Iniciando reparación ($(date)) ==="
echo "ROOT_DIR: $ROOT_DIR"

# 1. Permisos de escritura para Samba (Z:)
chmod a+rwx "$ROOT_DIR"
chmod 666 "$ROOT_DIR/Dockerfile" "$ROOT_DIR/php.ini" "$ROOT_DIR/docker-compose.yml" 2>/dev/null || true
chmod 644 "$ROOT_DIR/my.cnf" "$INFO_DIR/my.cnf" 2>/dev/null || true

# 2. Limpiar contenedores detenidos o renombrados por el bug ContainerConfig de docker-compose v1
echo "=== Limpiando contenedores previos de php_web y python_api ==="
docker rm -f php_web python_api 2>/dev/null || true
for cid in $(docker ps -aq --filter "name=php_web") $(docker ps -aq --filter "name=python_api"); do
    echo "Eliminando contenedor residual: $cid"
    docker rm -f "$cid" 2>/dev/null || true
done

# 3. Construir imágenes directamente con docker build (evita fallos de docker-compose v1)
echo "=== Construyendo imagen info-app:python312 ==="
docker build -t info-app:python312 "$ROOT_DIR/info/python_service"

echo "=== Construyendo imagen info-app:php82 ==="
docker build -t info-app:php82 "$ROOT_DIR"

# 4. Detectar la red Docker activa donde corre mysql_db
NET_NAME=$(docker inspect -f '{{range $k, $v := .NetworkSettings.Networks}}{{$k}}{{end}}' mysql_db 2>/dev/null | head -n 1)
if [ -z "$NET_NAME" ]; then
    NET_NAME="proyectos_proyecto-net"
    docker network create "$NET_NAME" 2>/dev/null || true
fi
echo "=== Red Docker detectada: $NET_NAME ==="

# 5. Intentar levantar con docker compose (--no-build --no-recreate)
cd "$ROOT_DIR"
echo "=== Levantando contenedores ==="
if docker compose version >/dev/null 2>&1; then
    docker compose up -d --no-build --no-recreate python_api web || true
else
    docker-compose up -d --no-build --no-recreate python_api web || true
fi

# 6. Fallback directo con docker run si docker-compose v1 falló con ContainerConfig
if ! docker ps --format '{{.Names}}' | grep -q '^python_api$'; then
    echo "=== [Fallback] Iniciando python_api directamente con docker run ==="
    docker rm -f python_api $(docker ps -aq --filter "name=python_api") 2>/dev/null || true
    docker run -d \
        --name python_api \
        --restart always \
        --network "$NET_NAME" \
        -p 8000:8000 \
        -e API_SECRET_TOKEN="9035232260ca3fc11faa82b6b32b20605fe55c242e6903ca2f7e0887c3e9be17" \
        -e DB_HOST="172.10.18.128" \
        -e DB_NAME="informes" \
        -e DB_USER="root" \
        -e DB_PASS="AdmSys2014" \
        -e DB_PORT="3306" \
        -v "$ROOT_DIR/info/python_service/app:/app/app" \
        info-app:python312
fi

if ! docker ps --format '{{.Names}}' | grep -q '^php_web$'; then
    echo "=== [Fallback] Iniciando php_web directamente con docker run ==="
    docker rm -f php_web $(docker ps -aq --filter "name=php_web") 2>/dev/null || true
    docker run -d \
        --name php_web \
        --restart always \
        --network "$NET_NAME" \
        -p 9258:80 \
        -e API_SECRET_TOKEN="9035232260ca3fc11faa82b6b32b20605fe55c242e6903ca2f7e0887c3e9be17" \
        -e DB_HOST="172.10.18.128" \
        -e DB_NAME="informes" \
        -e DB_USER="root" \
        -e DB_PASS="AdmSys2014" \
        -e DB_PORT="3306" \
        -e PYTHON_API_URL="http://python_api:8000" \
        -v "$ROOT_DIR/info:/var/www/html" \
        -v "$ROOT_DIR/php.ini:/usr/local/etc/php/php.ini" \
        info-app:php82
fi

echo "=== Estado final de contenedores ==="
docker ps
echo "=== Verificando versión de PHP en php_web ==="
docker exec php_web php -v || docker logs --tail 30 php_web
chmod 666 "$LOG_FILE"
echo "=== Proceso finalizado ($(date)) ==="