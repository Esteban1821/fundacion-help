# Sistema de HelpDesk e Inventario - Fundación Univalle 🛠️

Este es un aplicativo web desarrollado en **Laravel 10/11** para gestionar requerimientos de soporte técnico y el inventario de equipos tecnológicos.

## Pasos para instalar y probar este proyecto localmente:

1. **Clonar el repositorio:**
   git clone https://github.com/TU-USUARIO/TU-REPOSITORIO.git

2. **Entrar a la carpeta del proyecto:**
   cd nombre-de-la-carpeta

3. **Instalar las dependencias de PHP:**
   composer install

4. **Instalar dependencias de Node (Tailwind/Alpine):**
   npm install
   npm run build

5. **Configurar el entorno:**
   * Haz una copia del archivo `.env.example` y renómbralo a `.env`.
   * Abre el `.env` y configura tu conexión a la base de datos MySQL (DB_DATABASE, DB_USERNAME, etc.).

6. **Generar la clave de la aplicación:**
   php artisan key:generate

7. **Crear las tablas en la base de datos:**
   php artisan migrate

8. **Correr el servidor local:**
   php artisan serve

¡Y listo! Puedes acceder desde tu navegador en `http://localhost:8000`.