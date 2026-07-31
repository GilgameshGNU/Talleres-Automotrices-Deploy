<div id="landingScreen">
        <nav class="landing-navbar fixed-top-custom" id="mainNavbar">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="landing-logo tilt-element">
                    <div class="landing-brand-icon">
                        <i class="fa-solid fa-wrench"></i>
                    </div>
                    <div class="landing-brand-text">
                        <span>Taller Automotriz</span>
                        <small>Gesti-n Premium</small>
                    </div>
                </div>
                <div class="landing-nav-links d-none d-md-flex align-items-center gap-4">
                    <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
                        class="nav-link">Inicio</a>
                    <a href="#"
                        onclick="document.getElementById('landingServices').scrollIntoView({behavior:'smooth'}); return false;"
                        class="nav-link">Servicios</a>
                    <a href="#"
                        onclick="document.getElementById('landingAbout').scrollIntoView({behavior:'smooth'}); return false;"
                        class="nav-link">Equipo</a>
                    <a href="#landingLocations" onclick="scrollToLocations(); return false;"
                        class="nav-link">Ubicación</a>
                </div>
                <div class="landing-nav-actions tilt-element">
                    <button onclick="showLoginScreen()" class="btn-login-nav">
                        <i class="fa-solid fa-right-to-bracket"></i> <span>Ingresar</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- ===== HERO ===== -->
        <div class="landing-hero">
            <div class="container">
                <div class="row align-items-center min-vh-75">
                    <div class="col-lg-6 landing-hero-content reveal">
                        <span class="badge-premium mb-3">Servicio de Excelencia <i
                                class="fa-solid fa-star text-warning ms-1"></i></span>
                        <h1 class="landing-title text-uppercase">LA MEJOR<br>TECNOLOGÍA<br>PARA<br>TU <span>VEHÍCULO</span></h1>
                        <p class="landing-subtitle">Una nueva experiencia en mantenimiento automotriz.
                            Transparencia, rapidez y calidad garantizada en cada reparación.</p>
                        <div class="landing-hero-actions mt-4 d-flex align-items-center gap-3">
                            <button class="btn-primary-3d text-dark" onclick="scrollToLocations()" style="background: linear-gradient(100deg, var(--accent-1), var(--accent-2));">
                                <i class="fa-solid fa-map-location-dot me-2"></i> Ver Sucursales
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="landing-3d-showcase reveal">
                            <div class="glass-card-3d tilt-element" id="heroCard">
                                <i class="fa-solid fa-car-side fa-4x car-icon-3d mb-4" style="font-size: 5rem;"></i>
                                <div class="glass-stats">
                                    <div class="stat-item"><i class="fa-solid fa-check text-success"></i> +10k
                                        Reparaciones</div>
                                    <div class="stat-item"><i class="fa-solid fa-star text-warning"></i> 4.9/5
                                        Calificación</div>
                                </div>
                                <div
                                    style="position:absolute; top:-20px; right:-20px; font-size:2.5rem; color:#38bdf8; animation: float3D 4s infinite alternate;">
                                    <i class="fa-solid fa-gear"></i>
                                </div>
                                <div
                                    style="position:absolute; bottom:-10px; left:-10px; font-size:2rem; color:#fbbf24; animation: float3D 5s infinite alternate-reverse;">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                            </div>
                            <div class="glow-orb orb-1"></div>
                            <div class="glow-orb orb-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SERVICIOS ===== -->
        <div id="landingServices" class="landing-services">
            <div class="container">
                <div class="text-center mb-5 reveal">
                    <span class="badge-premium mb-3">Lo que ofrecemos</span>
                    <h2 class="section-title">Nuestros Servicios</h2>
                    <p class="section-subtitle">Soluciones integrales para mantener tu vehículo en óptimas condiciones
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6 reveal">
                        <div class="service-card tilt-element">
                            <i class="fa-solid fa-oil-can fa-3x"></i>
                            <h5>Cambio de Aceite</h5>
                            <p>Mantenimiento preventivo con aceites sintéticos de primera calidad.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 reveal">
                        <div class="service-card tilt-element">
                            <i class="fa-solid fa-brake-warning fa-3x"></i>
                            <h5>Frenos y Suspensión</h5>
                            <p>Revisión y reparación de frenos, amortiguadores y dirección.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 reveal">
                        <div class="service-card tilt-element">
                            <i class="fa-solid fa-car-battery fa-3x"></i>
                            <h5>Diagnóstico Eléctrico</h5>
                            <p>Escaneo y solución de fallas eléctricas y electrónicas.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 reveal">
                        <div class="service-card tilt-element">
                            <i class="fa-solid fa-tools fa-3x"></i>
                            <h5>Reparación General</h5>
                            <p>Motor, transmisión, sistema de enfriamiento y más.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ACERCA DE ===== -->
        <div id="landingAbout" class="landing-about">
            <div class="container">
                <div class="text-center mb-5 reveal">
                    <span class="badge-premium mb-3">Conoce al Equipo</span>
                    <h2 class="section-title">Acerca de Nosotros</h2>
                    <p class="section-subtitle">Profesionales expertos apasionados por el mundo automotriz</p>
                </div>
                <div class="row g-5 justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="profile-card-3d tilt-element reveal">
                            <div class="profile-bg-glow"></div>
                            <div class="profile-image-wrapper">
                                <img src="https://i.pravatar.cc/300?img=11" alt="Luis Rene">
                            </div>
                            <div class="profile-content">
                                <h3>Luis Rene</h3>
                                <h4>Programador</h4>
                                <p>Más de 3 años de experiencia en desarrollo de software y sistemas web.</p>
                                <div class="profile-social">
                                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="profile-card-3d tilt-element reveal">
                            <div class="profile-bg-glow"></div>
                            <div class="profile-image-wrapper">
                                <img src="https://i.pravatar.cc/300?img=47" alt="Erivaldo Fuentes">
                            </div>
                            <div class="profile-content">
                                <h3>Erivaldo Fuentes</h3>
                                <h4>Desarrollo de Backend</h4>
                                <p>Especializado en Laravel, backend, bases de datos y programación en general.</p>
                                <div class="profile-social">
                                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="profile-card-3d tilt-element reveal">
                            <div class="profile-bg-glow"></div>
                            <div class="profile-image-wrapper">
                                <img src="https://i.pravatar.cc/300?img=33" alt="Moises Moreno">
                            </div>
                            <div class="profile-content">
                                <h3>Moises Moreno</h3>
                                <h4>Desarrollo de Frontend</h4>
                                <p>Apasionado del frontend, me gusta desarrollar interfaces bonitas, modernas y fáciles
                                    de usar.</p>
                                <div class="profile-social">
                                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== UBICACIONES ===== -->
        <div id="landingLocations" class="landing-locations">
            <div class="container">
                <div class="text-center mb-5 reveal">
                    <h2 class="section-title">Nuestra Ubicación</h2>
                    <p class="section-subtitle">Encuéntranos en nuestra sede principal y visita el taller de forma rápida.</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="location-map-card">
                            <div id="tallerMapa"></div>
                            <div class="location-map-info">
                                <h3 style="color:#fff; margin:0 0 .35rem;">Taller Automotriz</h3>
                                <p style="color:var(--text-2, #9db8b0); margin:0; font-size:.95rem;">
                                    3er Anillo, cerca del Mercado Abasto — Santa Cruz de la Sierra, Bolivia
                                </p>
                                <a href="https://www.google.com/maps?q=-17.7937012,-63.2035641"
                                   target="_blank" rel="noopener"
                                   style="display:inline-block; margin-top:.7rem; color:#b6f24a; font-size:.9rem; text-decoration:none; font-weight:600;">
                                    Ver en Google Maps →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== FOOTER ===== -->
        <footer class="landing-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="landing-logo mb-3">
                            <div class="landing-brand-icon">
                                <i class="fa-solid fa-wrench"></i>
                            </div>
                            <div class="landing-brand-text">
                                <span>Taller Automotriz</span>
                                <small>Gesti-n Premium</small>
                            </div>
                        </div>
                        <p style="color:var(--text-2);font-size:14px;">Comprometidos con la excelencia y la satisfacción
                            de nuestros clientes. Calidad y confianza en cada servicio.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Enlaces rápidos</h5>
                        <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'}); return false;">Inicio</a><br>
                        <a href="#"
                            onclick="document.getElementById('landingServices').scrollIntoView({behavior:'smooth'}); return false;">Servicios</a><br>
                        <a href="#"
                            onclick="document.getElementById('landingAbout').scrollIntoView({behavior:'smooth'}); return false;">Equipo</a><br>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Síguenos</h5>
                        <div class="footer-social d-flex gap-2">
                            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                        <p class="mt-3" style="font-size:14px;color:var(--text-2);">
                            <i class="fa-regular fa-envelope me-1"></i> contacto@tallerautomotriz.com<br>
                            <i class="fa-regular fa-clock me-1"></i> Lun - Vie 08:00 - 18:00
                        </p>
                    </div>
                </div>
                <div class="footer-bottom text-center">
                    &copy; 2026 Taller Automotriz. Todos los derechos reservados. |
                    <a href="#" style="color:var(--text-2);font-size:13px;">Aviso legal</a> ·
                    <a href="#" style="color:var(--text-2);font-size:13px;">Política de privacidad</a>
                </div>
            </div>
        </footer>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>

        function scrollToLocations() {
            document.getElementById('landingLocations').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Navbar scroll
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        });

        // Reveal on scroll
        const revealEls = document.querySelectorAll('.reveal');
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(el => io.observe(el));

        // Tilt 3D para todos los elementos .tilt-element
        document.querySelectorAll('.tilt-element').forEach(el => {
            el.style.transformStyle = 'preserve-3d';
            el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            el.addEventListener('mousemove', (e) => {
                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const cx = rect.width / 2;
                const cy = rect.height / 2;
                const rotateY = ((x - cx) / cx) * 4;
                const rotateX = -((y - cy) / cy) * 4;
                el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(0)`;
            });
            el.addEventListener('mouseleave', () => {
                el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            });
        });

        // Tilt especial para la tarjeta hero
        const heroCard = document.getElementById('heroCard');
        if (heroCard) {
            heroCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            heroCard.addEventListener('mousemove', (e) => {
                const rect = heroCard.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                heroCard.style.transform = `perspective(1000px) rotateX(${(-y * 4).toFixed(2)}deg) rotateY(${(x * 4).toFixed(2)}deg)`;
            });
            heroCard.addEventListener('mouseleave', () => {
                heroCard.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            });
        }

        // Mapa de ubicación del taller
        const tallerCoords = [-17.7937012, -63.2035641];
        const mapaEl = document.getElementById('tallerMapa');
        if (mapaEl && window.L) {
            const mapa = L.map('tallerMapa', {
                center: tallerCoords,
                zoom: 17,
                zoomControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                touchZoom: false
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                maxZoom: 19
            }).addTo(mapa);

            const iconoTaller = L.divIcon({
                className: '',
                html: `<div style="width:26px; height:26px; border-radius:50% 50% 50% 0; background:linear-gradient(100deg,#b6f24a,#22d3c5); transform:rotate(-45deg); box-shadow:0 0 14px rgba(182,242,74,0.7); border:2px solid #04100e;"></div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 26]
            });

            L.marker(tallerCoords, { icon: iconoTaller }).addTo(mapa)
                .bindPopup('<b>Taller Automotriz</b><br>3er Anillo, cerca del Mercado Abasto');
        }
    </script>
