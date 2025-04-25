# Task List Terra

Una aplicación web sencilla de gestión de tareas desarrollada con PHP nativo, MySQL y Tailwind CSS.

## 📋 Descripción

Task List Terra es una aplicación web que permite gestionar tareas diarias de forma sencilla. Permite crear, editar y eliminar tareas asignadas a diferentes usuarios, con una interfaz intuitiva y responsive.

## ✨ Características

- Listado de tareas con información del usuario asignado
- Agregar nuevas tareas
- Editar tareas existentes
- Eliminar tareas
- Interfaz responsive con Tailwind CSS
- Tabla de datos con ordenamiento y búsqueda (DataTables)
- Notificaciones con SweetAlert2

## 🛠️ Tecnologías utilizadas

- PHP 8.2+
- MySQL
- HTML5
- Tailwind CSS
- JavaScript
- jQuery
- DataTables
- SweetAlert2

## 📦 Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/Alberto-AFAC/task-list-terra.git
```

2. Navega al directorio del proyecto:

```bash
cd task-list-terra
```

3. Importa la base de datos incluida en el repositorio:

```bash
mysql -u username -p database_name < task-list-terra.sql
```

4. Inicia un servidor local:

```bash
php -S localhost:8000
```

5. Accede a la aplicación en tu navegador:

```
http://localhost:8000/index.php
```

## 📊 Estructura de la base de datos

La base de datos consta de dos tablas principales:

- **users**: Almacena la información de los usuarios

  - id (PK)
  - username
  - created_at
  - deleted_at

- **tasks**: Almacena la información de las tareas
  - id (PK)
  - task_name
  - user_id (FK)
  - created_at

## 📁 Estructura del proyecto

```
task-list-terra/
├── index.php          # Página principal de la aplicación
├── db.php             # Configuración de conexión a la base de datos
├── add.php            # Script para agregar tareas
├── edit.php           # Script para editar tareas
├── delete.php         # Script para eliminar tareas
├── task-list-terra.sql # Dump de la base de datos
└── README.md          # Este archivo
```

## 💻 Uso

1. En la página principal verás la lista de tareas existentes.
2. Para añadir una nueva tarea, haz clic en el botón "+ Agregar Nueva Tarea".
3. Para editar una tarea, haz clic en el icono de edición de la tarea correspondiente.
4. Para eliminar una tarea, haz clic en el icono de eliminación de la tarea correspondiente.

## 🔗 Enlaces

- [Repositorio en GitHub](https://github.com/Alberto-AFAC/task-list-terra.git)
