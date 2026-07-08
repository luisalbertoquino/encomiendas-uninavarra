# Encomiendas UNINAVARRA

Sistema de recepción y seguimiento de encomiendas para UNINAVARRA (Laravel 10 + MySQL).

## Funcionalidades

- Registro de encomiendas con generación automática de código de seguimiento y QR.
- Bandeja con filtros por estado (recibida / notificada / entregada) e historial de cambios.
- Notificación por WhatsApp (enlace precargado) y correo automático al registrar.
- Consulta pública sin login: cualquier interesado ve sus encomiendas pendientes con solo su número de documento.
- Exportación de histórico en CSV para soporte administrativo.
- Login único de recepción (sin registro público).

## Stack

PHP 8.1 · Laravel 10 · MySQL 8 · Blade + CSS propio (sin frameworks de frontend) · Laravel Breeze (auth).

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
