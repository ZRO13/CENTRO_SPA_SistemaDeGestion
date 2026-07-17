<div align="center">
  <img src="https://campusvirtual2.ug.edu.ec/altlogin/assets/images/logo/UgHorizontalColor.svg" alt="Universidad de Guayaquil" width="350">

  <h1>Sistema de Gestión</h1>
  <p><em>Proyecto Universitario — Arquitectura MVC, DAO y DTO</em></p>
</div>

Proyecto desarrollado en **PHP 8** implementando arquitectura **MVC**, el patrón **DAO** (Data Access Object), **DTO** (Data Transfer Object) y **Front Controller**. Está construido completamente sin frameworks externos (PHP puro + PDO + JavaScript Vanilla), enfocándose en código estructurado, limpio y escalable.

El sistema integra de forma completa: Autenticación, Usuarios, Empleados, Clientes, Servicios, Citas, Productos, Compras y Reporte de Ventas.

---

## ⚙️ Módulos del Sistema

- **🔐 Autenticación:** Login, logout y registro público de clientes. Seguridad manejada con `password_hash()` / `password_verify()` y `$_SESSION`. Control de acceso estricto por roles (Administrador, Colaborador, Cliente) gestionado mediante `controllers/SesionHelper.php`.
- **👥 Gestión de Personal y Clientes:** CRUD completo con búsqueda, edición y baja lógica. La información personal base se centraliza en la tabla `usuarios`; las tablas `empleados` y `clientes` solo manejan atributos específicos para evitar duplicidad de datos.
- **📅 Citas:** Autogestión de reservas por parte de los clientes. El Administrador cuenta con una agenda centralizada para asignar colaboradores y actualizar estados.
- **🛍️ Productos y Catálogo:** CRUD administrativo de inventario y catálogo público con carrito de compras para el cliente.
- **🛒 Compras:** Flujo de transacciones desde el carrito (`compras` / `detalle_compra`). El cliente tiene acceso a su historial en la sección "Mis compras".
- **📊 Ventas:** Reporte administrativo de solo lectura que detalla las compras realizadas por los clientes.

---

##  Tecnologías Utilizadas

- **Backend:** PHP 8.x + PDO
- **Base de Datos:** Postgres / MySQL
- **Frontend:** HTML5, CSS3, JavaScript Vanilla (ES6)

---

##  Instalación y Configuración

1. Clona este repositorio dentro de la carpeta pública de tu servidor local (ej. `htdocs` en XAMPP o `www` en WAMP).
2. Importa la base de datos:
   * Abre phpMyAdmin (o tu gestor de DB preferido).
   * Ejecuta el script `sql/spa_belleza_db.sql`. *(Este script crea la base de datos, las tablas y carga datos de prueba automáticamente).*
3. Ajusta las credenciales de conexión en el archivo `config/Database.php` si tu entorno local lo requiere (por defecto utiliza usuario `root` sin contraseña).
4. Abre el proyecto en el navegador desde el archivo raíz (`index.php`).

---

##  Credenciales de Prueba

** Administrador**
| Usuario | Correo | Contraseña |
| :--- | :--- | :--- |
| `admin` | admin@spabelleza.com | `admin123` |

** Empleados (Colaboradores para asignar citas)**
| Usuario | Rol | Contraseña |
| :--- | :--- | :--- |
| `vrojas` | Cosmetóloga | `empleado123` |
| `dparedes` | Estilista | `empleado123` |
| `asalazar` | Masajista Terapéutico | `empleado123` |

---

##  Mapa de Rutas Principales (Front Controller)

| Ruta | Descripción | Acceso |
| :--- | :--- | :--- |
| `?controller=sitio&action=inicio` | Página de inicio | Público |
| `?controller=sitio&action=servicios` | Catálogo de Servicios | Público |
| `?controller=clienteProd&action=catalogo`| Catálogo público de productos | Público |
| `?controller=auth&action=login` | Iniciar sesión | Público |
| `?controller=auth&action=registro` | Registro de clientes | Público |
| `?controller=auth&action=logout` | Cerrar sesión | Todos |
| `?controller=admin&action=dashboard` | Panel administrativo | Admin / Colaborador |
| `?controller=usuario&action=listar` | CRUD de Usuarios | Administrador |
| `?controller=empleado&action=listar` | CRUD de Empleados | Administrador |
| `?controller=cliente&action=listar` | CRUD de Clientes | Administrador |
| `?controller=producto&action=listar` | CRUD de Productos | Administrador |
| `?controller=servicio&action=listar` | CRUD de Servicios | Administrador |
| `?controller=venta&action=index` | Reporte de Ventas | Administrador |
| `?controller=citas&action=index` | Gestión central de Citas | Administrador |
| `?controller=area-cliente&action=inicio` | Área privada del Cliente | Cliente |
| `?controller=area-cliente&action=compras`| Historial de compras | Cliente |
| `?controller=citas&action=miAgenda` | Agenda de citas personal | Cliente |
| `?controller=citas&action=crear` | Reservar nueva cita | Cliente |
| `?controller=carrito&action=*` | Funciones del carrito (AJAX) | Cliente |

---

##  Estructura del Proyecto

```text
├── config/
│   └── Database.php                 # Conexión PDO
├── controllers/
│   ├── SesionHelper.php             # Gestión de sesión y middleware de roles
│   ├── auth/                        # Controladores de acceso (Login, registro)
│   ├── admin/                       # Dashboard, CRUDs y panel de administración
│   ├── publico/                     # Controladores del sitio público
│   └── cliente/                     # Área privada, catálogo y carrito
├── models/
│   ├── dao/                         # Data Access Object (Lógica de base de datos)
│   └── dto/                         # Data Transfer Object (Entidades)
├── views/
│   ├── auth/                        # Vistas de sesión
│   ├── publico/                     # Vistas accesibles sin login
│   ├── cliente/                     # Vistas del perfil del cliente
│   └── admin/                       # Vistas administrativas
├── assets/
│   ├── css/design-system/           # Variables, componentes compartidos y base
│   └── css/                         # Hojas de estilo modulares por vista
└── sql/
    └── spa_belleza_db.sql           # Script de estructura y datos semilla