#!/bin/bash
# Script para subir cambios locales a GitHub.
# Uso: ./subir-cambios.sh "mensaje del commit"

set -e

MENSAJE="$1"
if [ -z "$MENSAJE" ]; then
  echo "Uso: ./subir-cambios.sh \"mensaje del commit\""
  exit 1
fi

git add -A
git status

echo ""
read -p "¿Confirmar commit y push con este mensaje? (\"$MENSAJE\") [s/N] " CONFIRMA
if [ "$CONFIRMA" != "s" ] && [ "$CONFIRMA" != "S" ]; then
  echo "Cancelado."
  exit 1
fi

git commit -m "$MENSAJE"
git push origin main

echo "Cambios subidos a GitHub."
