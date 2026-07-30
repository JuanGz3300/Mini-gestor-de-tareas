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

//Eliminar tarea
    if(isset($_GET['eliminar'])){
        $id_eli = $_GET['eliminar'];
        $tareas_actuales = obtTareas($archivo_json);
        
        //Filtramos tarea por ID desde el array
        $tareas_actuales = array_values(array_filter($tareas_actuales, function($t) use ($id_eli){
            return $t['id'] != $id_eli;
        }));
        

        guaTareas($archivo_json, $tareas_actuales);
        header("Location: index.php");
        exit();
    }

//Cambiamos estados a completada

    if(isset($_GET['completar'])){
        $id_comp = $_GET['completar'];
        $tareas_actuales = obtTareas($archivo_json);

        foreach($tareas_actuales as &$t){
            if($t['id'] == $id_comp){
                $t['completada'] = !$t['completada'];
            }
        }
        unset($t);

        guaTareas($archivo_json, $tareas_actuales);
        header("Location: index.php");
        exit();
    }
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
                            <!-- Actualizamos seccion de la lista segun cambios que haga el usuario -->
                            <ul class="list-group">
                                <?php foreach ($tarea as $t): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span class="<?php echo $t['completada'] ? 'text-decoration-line-through text-muted' : ''; ?>">
                                            <?php echo $t['titulo']; ?>
                                        </span>
                                        <div>
                                            <?php if ($t['completada']): ?>
                                                <span class="badge bg-success me-2">Completada</span>
                                                <a href="index.php?completar=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Marcar pendiente">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary me-2">Pendiente</span>
                                                <a href="index.php?completar=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-success" title="Completar">
                                                    <i class="fa-solid fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="index.php?eliminar=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger ms-1" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- script final para integrar la API de Advice Slip -->

    <script>
        $(document).ready(function(){
            //Interceptamos el click de los botones completado
            $('.btn-outline-success').on('click', function(e){
            //
            e.preventDefault();
            const urlAccion = $(this).attr('href');

            //consultamos la Api publica de Advice Slip
                $.ajax({
                    url: 'https://api.adviceslip.com/advice',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response){
                        let consejo = response.slip.advice;

                        //Mostramos la alerta incluyendo el consejo de la API

                        Swal.fire({
                            title: 'Excelente trabajo, sigue asi tu puedes',
                            text: 'Has completado una tarea, aqui tienes un gran consejo: "'+consejo+'"',
                            icon: 'success',
                            confirmButtonText: 'Super!'
                        }).then((result) =>{
                            //Cerramos la alerta y redirigimos a PHP actualizando el estado del json
                            window.location.href = urlAccion;
                        });
                    },
                    error: function(){
                        //Si falla la API completamos de igual manera la tareas sin bloquear a el usuario
                        window.location.href = urlAccion;
                    }
                });
            });
        });
    </script>
</body>
</html>