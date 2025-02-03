<?php
require_once 'config.php';
session_start();

// Configuración de paginación
$items_por_pagina = 1;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

// Modificar la función getImages para incluir paginación
function getImagesWithPagination($offset, $items_por_pagina) {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM images ORDER BY created_at DESC LIMIT $offset, $items_por_pagina");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener el total de imágenes para la paginación
function getTotalImages() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM images");
    return $stmt->fetchColumn();
}

$total_imagenes = getTotalImages();
$total_paginas = ceil($total_imagenes / $items_por_pagina);

// Procesar formularios y redirigir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload']) && isset($_FILES['image'])) {
        $result = uploadImage($_FILES['image']);
        $_SESSION['message'] = $result == "success" ? 
            ["type" => "success", "text" => "Imagen subida exitosamente."] : 
            ["type" => "danger", "text" => $result];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    else if (isset($_POST['toggle'])) {
        toggleVisibility($_POST['toggle']);
        header("Location: " . $_SERVER['PHP_SELF'] . "?pagina=" . $pagina_actual);
        exit();
    }
    else if (isset($_POST['delete'])) {
        deleteImage($_POST['delete']);
        header("Location: " . $_SERVER['PHP_SELF'] . "?pagina=" . $pagina_actual);
        exit();
    }
}

// Recuperar mensaje de la sesión
$message = '';
if (isset($_SESSION['message'])) {
    $messageData = $_SESSION['message'];
    $message = "<div class='alert alert-{$messageData['type']}'>{$messageData['text']}</div>";
    unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Administrar Carrusel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Administrar Imágenes del Carrusel</h2>
        
        <!-- Mostrar mensaje si existe -->
        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        
        <!-- Formulario para subir imagen -->
        <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="image" class="form-label">Seleccionar Imagen:</label>
                <input type="file" class="form-control" name="image" required>
            </div>
            <button type="submit" name="upload" class="btn btn-primary">Subir Imagen</button>
        </form>

        <!-- Lista de imágenes -->
        <div class="row">
            <?php
            $images = getImagesWithPagination($offset, $items_por_pagina);
            foreach($images as $image): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($image['filename']); ?>" class="card-img-top" alt="Carousel Image" 
                             style="opacity: <?php echo $image['visible'] ? '1' : '0.5'; ?>">
                        <div class="card-body">
                            <form method="POST" class="d-inline">
                                <button type="submit" name="toggle" value="<?php echo $image['id']; ?>" 
                                        class="btn btn-warning btn-sm">
                                    <?php echo $image['visible'] ? 'Ocultar' : 'Mostrar'; ?>
                                </button>
                                <button type="submit" name="delete" value="<?php echo $image['id']; ?>" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegación de imágenes" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($pagina_actual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>">
                        &laquo; Anterior
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>">
                        Siguiente &raquo;
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    </div>
</body>
</html>