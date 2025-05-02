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
git clone https://github.com/usuario/bit-and-brain-backend.git
cd bit-and-brain-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
