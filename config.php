<?php
// config.php
$host = 'localhost';
$dbname = 'carrusel';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Función para subir imagen
function uploadImage($file) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Validar que sea una imagen
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return "El archivo no es una imagen.";
    }
    
    // Validar extensión
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return "Solo se permiten archivos JPG, JPEG, PNG & GIF.";
    }
    
    // Subir archivo
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO images (filename) VALUES (?)");
        $stmt->execute([basename($file["name"])]);
        return "success";
    } else {
        return "Error al subir el archivo.";
    }
}

// Función para obtener todas las imágenes
function getImages() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM images ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para cambiar visibilidad
function toggleVisibility($id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE images SET visible = NOT visible WHERE id = ?");
    return $stmt->execute([$id]);
}

// Función para eliminar imagen
function deleteImage($id) {
    global $pdo;
    // Primero obtener el nombre del archivo
    $stmt = $pdo->prepare("SELECT filename FROM images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($image) {
        // Eliminar archivo físico
        $file_path = "uploads/" . $image['filename'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Eliminar registro de la base de datos
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
        return $stmt->execute([$id]);
    }
    return false;
}
?>