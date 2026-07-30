<?php
//Archivo para guardar tareas
    $archivo_json = 'tareas.json';

//Funcion para leer tareas
    function obtTareas($archivo){
        if(!file_exists($archivo)){
            return [];
        }
        $cont = file_get_contents($archivo);
        return json_decode($cont, true) ?: [];
    }

//Funcion para guardar tareas

    function guaTareas($archivo, $tarea){
        file_put_contents($archivo, json_encode($tarea, JSON_PRETTY_PRINT));
    }

//Proceso para reconocer cuando envian formulario y guardar tareas

    if (isset($_POST['agregar_tarea'])){
        $titu = trim($_POST['titulo_tarea']);
        if (!empty($titu)){
            $tarea = obtTareas($archivo_json);

            $nueva_tarea = [
                'id' => time(),
                'titulo' => htmlspecialchars($titu),
                'completada' => false
            ];

            //Inicio de array
            array_unshift($tarea, $nueva_tarea);
            guaTareas($archivo_json, $tarea);

            header("Location: index.php");
            exit();
        }
    }
//Lista actual de tareas    
    $tarea = obtTareas($archivo_json);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mini Gestor de Tareas</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <link rel="stylesheet" type="text/css" href="css/style.css">

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-12 col-md-8">

                <!--Titulo Principal-->

                <div class="text-center">
                    <h2 class="fw-bold text-primary"><i class="fa-solid fa-list-check me-2"></i>Mini Gestor de Tareas Inteligente</h2>
                    <p class="text-muted">Organiza tus pendientes y recibe un consejo al completarlos</p>
                </div>

                <!-- Formulario para agregar tarea -->

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="" method="POST" class="row g-3">
                            <div class="col-md-9">
                                <input type="text" name="titulo_tarea" class="form-control" placeholder="¿Que tarea tienes pendiente hoy?" required>
                            </div>
                            <div class="col-md-9">
                                <button type="submit" name="agregar_tarea" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Tareas -->
                <!-- Conectamos frontend con backend para funciones por medio de php --> 

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-secondary"><i class="fa-solid fa-tasks me-2"></i>Mis Tareas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($tarea)): ?>
                            <div class="alert alert-info mb-0" role="alert">
                                <i class="fa-solid fa-circle-info me-1"></i> Aun no hay tareas registradas. ¡Agrega una arriba!
                            </div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($tarea as $t): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span><?php echo $t['titulo']; ?></span>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>