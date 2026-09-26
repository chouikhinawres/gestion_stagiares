<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Personnelle Stage - Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="acc.css?v=<?= time() ?>">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">
                <i class="fas fa-graduation-cap text-primary me-2"></i>
                Gestion Stage
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-primary px-4 rounded-pill" href="auth/form_login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero min-vh-100 d-flex align-items-center text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4 hero-title">
                        Gestionnaire de <span class="text-primary">stages</span> intelligent
                    </h1>
                    <p class="lead mb-4 hero-subtitle">
                        Gagnez en efficacité grâce à une plateforme conçue
                        pour structurer et simplifier la gestion des stages.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="auth/form_login.php" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                            <i class="fas fa-rocket me-2"></i>Commencer
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                            <i class="fas fa-play-circle me-2"></i>Découvrir
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-users fa-10x opacity-25 hero-icon"></i>
                        <div class="hero-badge">
                            <i class="fas fa-chart-line me-2"></i>
                            Suivi & Analyse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Fonctionnalités puissantes</h2>
                    <p class="lead text-muted">Une plateforme complète pour gérer tous vos stages efficacement</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body text-center p-5">
                            <div class="icon-circle mb-4 mx-auto">
                                <i class="fas fa-user-graduate text-primary fa-3x"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Dashboard Stagiaire</h4>
                            <p class="card-text text-muted">
                                Candidature facile, suivi en temps réel, upload CV,
                                progression stage visible.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body text-center p-5">
                            <div class="icon-circle mb-4 mx-auto">
                                <i class="fas fa-user-shield text-success fa-3x"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Admin Panel</h4>
                            <p class="card-text text-muted">
                                Gestion demandes, affectation automatique,
                                suivi encadrants et projets.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body text-center p-5">
                            <div class="icon-circle mb-4 mx-auto">
                                <i class="fas fa-chart-line text-warning fa-3x"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">Suivi Temps Réel</h4>
                            <p class="card-text text-muted">
                                Stats dashboards, progression % stage,
                                notifications statut automatique.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Prêt à gérer vos stages ?</h2>
            <p class="lead mb-4">Plateforme professionnelle 100% gratuite et open-source</p>
            <a href="auth/form_login.php" class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Accéder à la plateforme
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white">
        <div class="container text-center">
                <a href="contact.html" class="text-white-50">Contact</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
