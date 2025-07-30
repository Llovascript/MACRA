<link rel="stylesheet" href="{{ asset('css/auth.css') }}">

@extends('layout')

@section('title', 'Iniciar Sesión - MACRA')

@section('content')
    <div class="auth-container">
        <div class="auth-wrapper">
            <!-- Lado izquierdo - Información -->
            <div class="auth-info-side">

                <div class="logo-container">
                    <img src="{{ asset('images/logo-MACRA.jpg') }}" alt="MACRA" class="logo-icon">
                    <div class="logo-text">
                        <span class="logo-main">MACRA</span>
                        <span class="logo-sub">BANCO DE<br>ALIMENTOS</span>
                    </div>
                </div>


                <div class="welcome-message">
                    <h2>¡Cada alimento cuenta, cada persona importa!</h2>
                </div>

                <div class="description">
                    <p>Bienvenido a MACRA, un espacio donde tu ayuda se transforma en esperanza.</p>
                </div>
            </div>

            <!-- Lado derecho - Formulario -->
            <div class="auth-form-side">
                <div class="form-container">
                    <div class="back-button">
                        <a href="{{ url('/') }}" class="back-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>

                    <div class="form-content">
                        <h1 class="form-title">¡Bienvenido de nuevo!</h1>

                        <form class="auth-form" method="POST" action="#">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-input" placeholder="Correo electrónico"
                                    required>
                                <div class="input-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"
                                            stroke="currentColor" stroke-width="2" />
                                        <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
                                <div class="input-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"
                                            stroke="currentColor" stroke-width="2" />
                                        <circle cx="12" cy="16" r="1" fill="currentColor" />
                                        <path d="M7 11V7A5 5 0 0 1 17 7V11" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>

                            <button type="submit" class="submit-button">
                                Iniciar Sesión
                            </button>
                        </form>

                        <div class="form-links">
                            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>

                            <div class="register-link">
                                <span>¿Aún no tienes una cuenta?</span>
                                <a href="{{ route('register') }}">Únirse al Equipo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
