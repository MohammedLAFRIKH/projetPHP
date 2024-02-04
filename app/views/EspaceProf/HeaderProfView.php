<?php

namespace App\Views\EspaceProf;

class HeaderProfView 
{
    public function showHeader($isUserConnected,$title)
    {
        ?>
        
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title><?php echo $title; ?></title>
            <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            
        <header>
            <div class="header">
                <div class="header-content">
                    <a href="/apogee_ens/"><img src="<?= BASE_URL ?>/public/images/logo_ens_rabat.png" class="logo"></a>
                    <div class="float-right menu-block js-menu-items hide-in-mobile">
                        <div class="menu-links">
                            <?php if ($isUserConnected): ?>
                                <!-- User is connected, show modify information link -->
                                <a href="/apogee_ens/espaceprof/details" class="menu-header">Accueil</a>
                                <a href="/apogee_ens/infoprof" class="menu-header">Modifier mes informations</a>
                                <a href="/apogee_ens/removesessions" class="menu-header show-in-mobile hide-desktop">Quitter</a>
                            <?php else: ?>
                                <!-- User is not connected, show create account and login links -->
                                <a href="/apogee_ens/" class="menu-header">Accueil</a>
                                <a href="/apogee_ens/login" class="menu-header">Se connecter</a>
                            <?php endif; ?>
                        </div>
                    </div>  
                </div>
            </div>
         </header>

        <div class="container-fluid">
            <div class="">
                <!-- Sidebar -->
                <?php if ($isUserConnected): ?>
                    <div id="wrapper">

                <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="/apogee_ens/espaceprof/avis">
                                    Avis <i class="bi bi-star-fill text-warning"></i>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="/apogee_ens/espaceprof/annonce">
                                    Mes annonce <i class="bi bi-star-fill text-warning"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/apogee_ens/espaceprof/emploi">
                                    Les EDTs <i class="bi bi-star-fill text-warning"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/apogee_ens/espaceprof/showAllemploiHtml">
                                     changement d'horaire <i class="bi bi-star-fill text-warning"></i>
                                </a>
                            </li>
                            <!-- Ajoutez d'autres éléments de la barre latérale si nécessaire -->
                        </ul>
                    </div>
                    <?php endif; ?>

                </nav>
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">


        <?php
    }
}
