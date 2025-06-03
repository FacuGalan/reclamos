Proyecto Laravel Livewire
Requisitos previos

XAMPP (Apache, MySQL, PHP 8.1+)
Composer
Node.js y npm
Git

Instalación local
1. Clonar el repositorio
bashgit clone https://github.com/tu-usuario/nombre-repositorio.git
cd nombre-repositorio
2. Instalar dependencias de PHP
bashcomposer install
3. Instalar dependencias de Node.js
bashnpm install
4. Configurar el archivo de entorno
bashcp .env.example .env
5. Generar la clave de aplicación
bashphp artisan key:generate
6. Configurar la base de datos

Inicia XAMPP y activa Apache y MySQL
Crea una base de datos en phpMyAdmin
Edita el archivo .env con tus datos de conexión:

envDB_DATABASE=nombre_tu_base_datos
DB_USERNAME=root
DB_PASSWORD=
7. Ejecutar migraciones
bashphp artisan migrate
8. Compilar assets
bashnpm run dev
9. Iniciar el servidor de desarrollo
bashphp artisan serve
El proyecto estará disponible en http://localhost:8000
Flujo de trabajo para colaboradores
Antes de trabajar
bashgit pull origin main
composer install
npm install
Después de hacer cambios
bashgit add .
git commit -m "Descripción de los cambios"
git push origin main
Comandos útiles

php artisan serve - Iniciar servidor de desarrollo
npm run dev - Compilar assets para desarrollo
npm run build - Compilar assets para producción
php artisan migrate:fresh - Recrear base de datos
php artisan make:livewire NombreComponente - Crear componente Livewire