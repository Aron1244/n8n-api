# 🧠 Bit and Brain - Laravel Backend

Este es el backend del proyecto **Bit and Brain**, desarrollado en **Laravel 12**. Proporciona una API para la gestión de usuarios, pagos con **PayPal**, y funcionalidades clave del sistema.

---

## ⚙️ Requisitos

- **PHP >= 8.2.7**  
- **Composer**  
- **Laravel 12**  
- **MariaDB**  
- Extensiones PHP necesarias:
  - `openssl`
  - `mbstring`
  - `pdo`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`

---

## 📦 Instalación

```bash
git clone https://github.com/Aron1244/n8n-api
cd n8n-api
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link

---

## 🛠️ Migraciones y Seeders

```bash
php artisan migrate --seed


---

## 🛠️ Limpiar cache y rutas

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

---

## 💳 Configuracion de paypal en variables de entorno

```bash

PAYPAL_CLIENT_ID=tu_client_id_aqui
PAYPAL_SECRET=tu_secret_aqui
PAYPAL_MODE=sandbox # o 'live' para producción
PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com # usar https://api-m.paypal.com en producción

