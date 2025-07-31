<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffee Website | LauLua')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Header / Navbar -->
    <header>
        <nav class="navbar section-content">
            <a href="{{ url('/') }}" class="nav-logo">
                <img src="{{ asset('images/logo-MACRA.jpg') }}" alt="MACRA" class="logo-icon">
                <h2 class="logo-text">MACRA</h2>
            </a>

            <!-- Submenú -->
            <ul class="nav-menu">
                <button id="menu-close-button" class="fas fa-times"></button>

                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link">Hogar</a>
                </li>

                <li class="nav-item">
                    <a href="#about" class="nav-link">Acerca de</a>
                </li>

                <li class="nav-item">
                    <a href="#testimonials" class="nav-link">Testimonios</a>
                </li>

                <li class="nav-item">
                    <a href="#gallery" class="nav-link">Galería</a>
                </li>

                <li class="nav-item">
                    <a href="#contact" class="nav-link">Contacto</a>
                </li>
            </ul>

            <button id="menu-open-button" class="fas fa-bars"></button>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- JavaScript personalizado -->
    <script src="{{ asset('js/script.js') }}"></script>

    @stack('scripts')
</body>

</html>
