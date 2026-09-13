# Mesa de Ayuda — Fundación Universidad del Valle

Aplicativo web para la gestión centralizada de solicitudes de soporte técnico e
inventario tecnológico, desarrollado como trabajo de grado de Tecnología en
Sistemas de Información de la Institución Universitaria Antonio José Camacho.

## Descripción

El sistema reemplaza la atención de solicitudes por canales informales (correo,
llamadas y comunicación verbal) por un registro centralizado que permite hacer
seguimiento a cada caso mediante un número de radicado, clasificarlo según un
catálogo de servicios parametrizable y medir el desempeño del área a través de
indicadores de gestión.

## Módulos

- **Tickets** — registro con catálogo de servicios, prioridad y archivo adjunto;
  ciclo de estados Abierto → Atendido → Cerrado.
- **Calificación del servicio (CSAT)** — el solicitante califica de 1 a 5 al
  cerrar el caso.
- **Inventario tecnológico** — registro de equipos, asignación a funcionarios y
  exportación a PDF.
- **Panel de indicadores** — tickets abiertos, resueltos del día, satisfacción
  promedio y tiempo de resolución por técnico, con actualización automática.
- **Gestión de usuarios** — tres roles con permisos diferenciados.

## Tecnologías

Laravel 13 · PHP 8.3 · MySQL · Blade · Tailwind CSS · Alpine.js · Chart.js · DomPDF

## Requisitos

| Programa | Versión mínima |
|---|---|
| PHP | 8.3 |
| Composer | 2.x |
| MySQL | 5.7 o MariaDB 10.4 |
| Git | 2.x |

## Instalación

```bash
git clone https://github.com/Esteban1821/fundacion-help.git
cd fundacion-help
composer install
cp .env.example .env          # en Windows: copy .env.example .env
php artisan key:generate
```

Cree la base de datos `fundacion_help` en MySQL y verifique las credenciales en
el archivo `.env`. Luego:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

El aplicativo queda disponible en `http://127.0.0.1:8000`.

## Usuarios de prueba

| Rol | Usuario | Contraseña |
|---|---|---|
| Administrador | `dgiron` | `123456789` |
| Soporte | `jperez` | `123456789` |
| Usuario | `cmartinez` | `123456789` |

El formulario de acceso solicita el **usuario institucional**, no el correo
electrónico. Estas credenciales corresponden a un entorno de pruebas.

## Pruebas automatizadas

```bash
php artisan test
```

Diecisiete pruebas que verifican el control de acceso por roles, la validación
de propiedad de los registros al calificar un ticket y la generación del número
de radicado. Se ejecutan sobre una base de datos temporal en memoria.

## Seguridad

- Control de acceso por roles mediante middleware, aplicado del lado del servidor.
- Verificación de propiedad del registro antes de permitir operaciones sobre él.
- Almacenamiento de archivos adjuntos fuera del directorio público, con descarga
  autenticada.
- Restricción de unicidad sobre la calificación de cada ticket.
- Validación de datos en el servidor para todos los formularios.

## Alcance

El sistema constituye un **prototipo funcional** validado en un entorno local de
pruebas. No se encuentra desplegado sobre un servidor de producción ni ha sido
sometido a condiciones de uso concurrente.

## Autores

David Girón Acosta · James David Ortiz Muñoz · Esteban Parada Gonzales

Directora: Ing. Claudia Patricia Muñoz Guerrero
