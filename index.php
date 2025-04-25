<?php
require_once 'db.php';

// Consulta para obtener todas las tareas con información de usuario
$stmt = $pdo->prepare("
    SELECT t.id, t.user_id,t.task_name, t.created_at, u.username 
    FROM tasks t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC
");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener todos los usuarios
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE deleted_at IS NULL ORDER BY username");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive-dt/2.4.1/responsive.dataTables.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive/2.4.1/dataTables.responsive.min.js"></script>
    <title>Lista de tareas</title>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="container mx-auto max-w-5xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-600 p-4">
                <h1 class="text-2xl font-bold text-white">Lista de Tareas</h1>
                <p class="text-blue-100">Gestiona tus tareas diarias de forma sencilla</p>
            </div>

            <!-- Sección para añadir nueva tarea -->
            <div class="p-4 border-b">
                <button id="openModalBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                    + Agregar Nueva Tarea
                </button>
            </div>

            <!-- Tabla de tareas con DataTables -->
            <div class="p-4">
                <table id="tasksTable" class="display responsive nowrap w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="d-none">ID USUARIO</th>
                            <th>Tarea</th>
                            <th>Fecha de creación</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tasks) > 0): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['id']) ?></td>
                                    <td><?= htmlspecialchars($task['user_id']) ?></td>
                                    <td><?= htmlspecialchars($task['task_name']) ?></td>
                                    <td><?= htmlspecialchars($task['created_at']) ?></td>
                                    <td><?= htmlspecialchars($task['username']) ?></td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900 edit-task" data-id="<?= $task['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <a href="#" class="text-red-600 hover:text-red-900 delete-task" data-id="<?= $task['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer con información -->
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="text-sm text-gray-500">
                    Sistema de gestión de tareas
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar tarea -->
    <div id="taskModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Agregar Nueva Tarea</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="addTaskForm" class="space-y-4">
                <input type="hidden" name="task_id" id="task_id"><!-- Campo oculto para edición -->
                <div>
                    <label for="task_name" class="block text-sm font-medium text-gray-700">Nombre de la tarea</label>
                    <input type="text" name="task_name" id="task_name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                    <select name="user_id" id="user_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelBtn" class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            let tasksTable = $('#tasksTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'user_id',
                        visible: false
                    },
                    {
                        data: 'task_name'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        defaultContent: ''
                    }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {
                        return `
                                <div class="flex space-x-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 edit-task" data-id="${row.id}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="text-red-600 hover:text-red-900 delete-task" data-id="${row.id}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                </div>
                            `;
                    }
                }],
                // Convertir los datos existentes a formato DataTables
                fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    $('td', nRow).addClass('px-6 py-4 whitespace-nowrap text-sm');
                    return nRow;
                }
            });

            <?php if (count($tasks) > 0): ?>
                tasksTable.clear();
                <?php foreach ($tasks as $task): ?>
                    tasksTable.row.add({
                        id: <?= json_encode($task['id']) ?>,
                        user_id: <?= json_encode($task['user_id']) ?>,
                        task_name: <?= json_encode($task['task_name']) ?>,
                        created_at: <?= json_encode($task['created_at']) ?>,
                        username: <?= json_encode($task['username']) ?>
                    });
                <?php endforeach; ?>
                tasksTable.draw();
            <?php endif; ?>

            // Elementos del DOM
            const openModalBtn = document.getElementById('openModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const taskModal = document.getElementById('taskModal');
            const addTaskForm = document.getElementById('addTaskForm');
            const modalTitle = taskModal.querySelector('h3');
            const submitBtn = addTaskForm.querySelector('button[type="submit"]');

            // Abrir modal
            openModalBtn.addEventListener('click', function() {
                modalTitle.textContent = 'Agregar Nueva Tarea';
                submitBtn.textContent = 'Guardar';
                addTaskForm.reset();
                document.getElementById('task_id').value = '';
                taskModal.classList.remove('hidden');
            });

            $('#tasksTable').on('click', '.edit-task', function(e) {
                e.preventDefault();
                const rowData = tasksTable.row($(this).closest('tr')).data();
                modalTitle.textContent = 'Editar Tarea';
                submitBtn.textContent = 'Guardar Cambios';
                addTaskForm.reset();
                $('#task_id').val(rowData.id);
                $('#user_id').val(rowData.user_id || '1');
                $('#task_name').val(rowData.task_name);

                taskModal.classList.remove('hidden');
            });

            // Cerrar modal
            function closeModal() {
                taskModal.classList.add('hidden');
                addTaskForm.reset();
                document.getElementById('task_id').value = '';
            }
            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            taskModal.addEventListener('click', function(e) {
                if (e.target === taskModal) {
                    closeModal();
                }
            });

            taskModal.addEventListener('click', function(e) {
                if (e.target === taskModal) {
                    closeModal();
                }
            });

            addTaskForm.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData(addTaskForm);
                const isEdit = !!formData.get('task_id');
                const url = isEdit ? 'edit.php' : 'add.php';

                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            closeModal();

                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: isEdit ? 'La tarea ha sido actualizada correctamente' : 'La tarea ha sido añadida correctamente',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (isEdit) {
                                // Actualizar la fila en DataTable
                                let rowIdx = tasksTable.rows().eq(0).filter(function(idx) {
                                    return tasksTable.cell(idx, 0).data() == data.task.id;
                                });
                                if (rowIdx.length > 0) {
                                    tasksTable.row(rowIdx[0]).data({
                                        id: data.task.id,
                                        user_id: data.task.user_id,
                                        task_name: data.task.task_name,
                                        created_at: data.task.created_at,
                                        username: data.task.username
                                    }).draw(false);
                                }
                            } else {
                                // Añadir la nueva tarea a DataTable
                                const newRow = tasksTable.row.add({
                                    id: data.task.id,
                                    user_id: data.task.user_id,
                                    task_name: data.task.task_name,
                                    created_at: data.task.created_at,
                                    username: data.task.username,
                                }).draw().node();

                                $(newRow).addClass('bg-green-100');
                                setTimeout(function() {
                                    $(newRow).removeClass('bg-green-100');
                                }, 3000);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Ha ocurrido un error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ha ocurrido un error al procesar la solicitud'
                        });
                    });
            });

            // Eliminar tarea
            $('#tasksTable').on('click', '.delete-task', function(e) {
                e.preventDefault();
                const taskId = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar indicador de carga
                        Swal.fire({
                            title: 'Eliminando tarea...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('delete.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `task_id=${taskId}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Eliminada',
                                        text: 'La tarea ha sido eliminada correctamente',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Eliminar la fila de DataTable
                                    const row = tasksTable.row($(this).closest('tr'));
                                    row.remove().draw();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Ha ocurrido un error al eliminar la tarea'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ha ocurrido un error al procesar la solicitud'
                                });
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>