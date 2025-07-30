@extends('layout')

@section('title', 'MACRA | TeamMacra')

@section('content')
    <!-- Home section -->
    <section class="hero-section">
        <div class="section-content">
            <div class="hero-details">
                <h2 class="title">Alimentos que transforman vidas</h2>
                <h3 class="subtitle">!Haz que tu apoyo llegue a quienes más lo necesitan!</h3>
                <p class="description">Bienvenido a MACRA Banco de Alimentos, un lugar donde tu solidaridad se
                    transforma en alimento, apoyo y esperanza para quienes más lo necesitan.
                </p>

                <div class="buttons">
                    <a href="#" class="button order-now">Iniciar Sesión</a>
                    <a href="#" class="button contact-us">Regístrate</a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="{{ asset('images/manos-ayudando.png') }}" alt="Hero" class="hero-image">
            </div>
        </div>
    </section>

    <!-- About section -->
    <section class="about-section" id="about">
        <div class="section-content">
            <div class="about-image-wrapper">
                <img src="{{ asset('images/sobre-nosotros.jpg') }}" alt="About" class="about-image">
            </div>
            <div class="about-details">
                <h2 class="section-title">SOBRE NOSOTROS</h2>
                <p class="text">
                    MACRA es un proyecto que facilita la conexión entre personas, empresas y organizaciones
                    comprometidas con el apoyo a asociaciones de beneficiencia, a través de la donación voluntaria de
                    alimentos. Su propósito es contribuir a mejorar la seguridad alimentaria de personas y familias en
                    situación de vulnerabilidad.
                </p>

                <p class="text">
                    Este esfuerzo es posible gracias a la colaboración de empresas y asociaciones aliadas, al trabajo
                    logístico de voluntarios y colaboradores, y al valioso compromiso de quienes han aportado con sus
                    donaciones.
                </p>
                <div class="social-link-list">
                    <a href="#" class="social-link">
                        <i class="fa-brands fa-facebook"></i>
                    </a>

                    <a href="#" class="social-link">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#" class="social-link">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials section -->
    <section class="testimonials-section" id="testimonials">
        <h2 class="section-title">TESTIMONIOS</h2>
        <div class="section-content">
            <div class="slider-container swiper">
                <div class="slider-wrapper">
                    <ul class="testimonials-list swiper-wrapper">
                        @php
                            $testimonials = [
                                [
                                    'image' => 'user-1.jpg',
                                    'name' => 'Rosa M., madre de familia beneficiaria',
                                    'feedback' => '"Gracias a MACRA, mi familia ha podido tener alimentos en momentos muy díficles.
                                        No sabíamos a quién acudir, y hoy estamos profundamente agradecidos,"',
                                ],
                                [
                                    'image' => 'user-2.jpg',
                                    'name' => 'Carlos T,. colaborador de distribución',
                                    'feedback' => '"Con MACRA encontramos una foram real y efectivba de hacer responsabiliades.
                                        La logística, el compromiso y el impacto son admirables."',
                                ],
                                [
                                    'image' => 'user-3.jpg',
                                    'name' => 'Alejandro G., coordinador RSE en Grupo Alimentario',
                                    'feedback' => '"Colaborar con MACRA ha sido una experiencia transformadora para nuestra empresa.
                                        Saber que nuestros excedentes ayudan a quienes más lo necesitan nos motiva a seguir aportando."',
                                ],
                                [
                                    'image' => 'user-4.jpg',
                                    'name' => 'Andrea C., donadora voluntaria',
                                    'feedback' => '"Decidí donar alimentos a MACRA porque confío en su causa. Saber que mi ayuda llega a quienes
                                        lo necesitan me llena el corazón."',
                                ],
                                [
                                    'image' => 'user-5.jpg',
                                    'name' => 'Jorge H., donador voluntario',
                                    'feedback' => '"No hace falta tener mucho para ayudar. Con MACRA, entendí que cualquier aporte,
                                        por pequeño que sea, puede marcar la diferencia en la vida de alguien."',
                                ],
                            ];
                        @endphp

                        @foreach ($testimonials as $testimonial)
                            <li class="testimonial swiper-slide">
                                <img src="{{ asset('images/' . $testimonial['image']) }}" alt="User" class="user-image">
                                <h3 class="name">{{ $testimonial['name'] }}</h3>
                                <i class="feedback">{{ $testimonial['feedback'] }}</i>
                            </li>
                        @endforeach
                    </ul>

                    <div class="swiper-pagination"></div>
                    <div class="swiper-slide-button swiper-button-prev"></div>
                    <div class="swiper-slide-button swiper-button-next"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery section -->
    <section class="gallery-section" id="gallery">
        <h2 class="section-title">GALERÍA</h2>
        <div class="section-content">
            <ul class="gallery-list">
                @for ($i = 1; $i <= 6; $i++)
                    <li class="gallery-item">
                        <img src="{{ asset('images/galeria-' . $i . '.jpg') }}" alt="Gallery" class="gallery-image">
                    </li>
                @endfor
            </ul>
        </div>
    </section>

    <!-- Contact section -->
    <section class="contact-section" id="contact">
        <h2 class="section-title">CONTÁCTENOS</h2>
        <div class="section-content">
            <ul class="contact-info-list">
                @php
                    $contactInfo = [
                        [
                            'icon' => 'fa-solid fa-location-crosshairs',
                            'text' => '123 Avenida Constituyentes, Col. Cimatario, Querétaro, Qro. 76000',
                        ],
                        [
                            'icon' => 'fa-regular fa-envelope',
                            'text' => 'macra.team@gmail.com',
                        ],
                        [
                            'icon' => 'fa-solid fa-phone',
                            'text' => '(442) 123-4567',
                        ],
                        [
                            'icon' => 'fa-regular fa-clock',
                            'text' => 'Lunes - Viernes: 9:00 AM - 5:00 PM',
                        ],
                        [
                            'icon' => 'fa-regular fa-clock',
                            'text' => 'Sábado: 10:00 AM - 3:00 PM',
                        ],
                        [
                            'icon' => 'fa-regular fa-clock',
                            'text' => 'Domingo: Cerrado',
                        ],
                        [
                            'icon' => 'fa-solid fa-globe',
                            'text' => 'www.macraWeb.com',
                        ],
                    ];
                @endphp

                @foreach ($contactInfo as $info)
                    <li class="contact-info">
                        <i class="{{ $info['icon'] }}"></i>
                        <p>{{ $info['text'] }}</p>
                    </li>
                @endforeach
            </ul>

            <form action="#" class="contact-form">
                @csrf
                <input type="text" name="name" placeholder="Nombre" class="form-input" required>
                <input type="email" name="email" placeholder="Correo electrónico" class="form-input" required>
                <textarea name="message" placeholder="Mensaje" class="form-input" required></textarea>
                <button type="submit" class="submit-button">Entregar</button>
            </form>
        </div>
    </section>

    <!-- Footer section -->
    <footer class="footer-section">
        <div class="section-content">
            <p class="copyright-text">© {{ date('Y') }} MACRA Banco de ALimentos</p>

            <div class="social-link-list">
                <a href="#" class="social-link">
                    <i class="fa-brands fa-facebook"></i>
                </a>

                <a href="#" class="social-link">
                    <i class="fa-brands fa-instagram"></i>
                </a>

                <a href="#" class="social-link">
                    <i class="fa-brands fa-x-twitter"></i>
                </a>
            </div>

            <p class="policy-text">
                <a href="#" class="policy-link">Privacy policy</a>
            </p>
        </div>
    </footer>
@endsection
