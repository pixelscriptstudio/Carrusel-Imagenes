<?php
require_once 'config.php';
// Obtener solo las imágenes visibles
$stmt = $pdo->query("SELECT * FROM images WHERE visible = 1 ORDER BY created_at DESC");
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalImages = count($images);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrusel Premium Ultra</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./css/carrusel.css" />
  </head>
  <body>
    <div class="carousel-wrapper">
      <div class="container">
        <div class="carousel-container">
          <div class="slide-counter">
            <span id="currentSlide">1</span> / <span><?php echo $totalImages; ?></span>
          </div>

          <div
            id="carouselPremium"
            class="carousel slide"
            data-bs-ride="carousel"
          >
            <div class="carousel-indicators">
              <?php foreach($images as $index => $image): ?>
              <button
                type="button"
                data-bs-target="#carouselPremium"
                data-bs-slide-to="<?php echo $index; ?>"
                class="<?php echo $index === 0 ? 'active' : ''; ?>"
              ></button>
              <?php endforeach; ?>
            </div>

            <div class="carousel-inner">
              <?php foreach($images as $index => $image): ?>
              <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="uploads/<?php echo $image['filename']; ?>" class="d-block w-100" alt="Imagen <?php echo $index + 1; ?>" />
                <div class="progress-bar"></div>
              </div>
              <?php endforeach; ?>
            </div>

            <?php if($totalImages > 1): ?>
            <button
              class="carousel-control-prev"
              type="button"
              data-bs-target="#carouselPremium"
              data-bs-slide="prev"
            >
              <span class="carousel-control-prev-icon"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button
              class="carousel-control-next"
              type="button"
              data-bs-target="#carouselPremium"
              data-bs-slide="next"
            >
              <span class="carousel-control-next-icon"></span>
              <span class="visually-hidden">Siguiente</span>
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="./js/carrusel.js"></script>
  </body>
</html>