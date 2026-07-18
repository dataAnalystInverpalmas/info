<?php
// error_404.php - Beautiful and professional 404 Error Page
?>
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-6 text-center">
            <div class="error-template">
                <!-- Large icon or illustrative graphic -->
                <div class="mb-4">
                    <svg width="180" height="180" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto text-muted">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z" fill="#dc3545" opacity="0.8"/>
                    </svg>
                </div>
                <h1 class="display-3 font-weight-bold text-dark mb-2">404</h1>
                <h2 class="h4 text-secondary mb-4">Página no encontrada</h2>
                <div class="error-details text-muted mb-5">
                    Lo sentimos, la sección o el reporte al que intenta acceder no existe, ha sido movido o no está disponible en este momento. Por favor, verifique la dirección.
                </div>
                <div class="error-actions">
                    <a href="index.php" class="btn btn-success btn-lg px-4 shadow-sm" style="background-color: #00796B; border-color: #00796B; transition: all 0.3s ease;">
                        <span class="material-icons align-middle mr-1" style="font-size: 1.25rem;">home</span> 
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-template {
        padding: 40px 15px;
        text-align: center;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .error-actions .btn:hover {
        background-color: #004D40 !important;
        border-color: #004D40 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 77, 64, 0.25) !important;
    }
</style>
