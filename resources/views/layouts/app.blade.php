<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') - Iglesia Actúa</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <?php
        $accesoActive = Request::is('users*') || Request::is('roles*');
        $paramActive = Request::is('miembros*') || Request::is('cultos*') || Request::is('actividades*') || Request::is('cursos*') || Request::is('cargos*') || Request::is('lideres*') || Request::is('ministerios*') || Request::is('docentes*') || Request::is('seguimiento*') || Request::is('contratos*');
        $transacActive = Request::is('inscripciones*') || Request::is('asistencia*') || Request::is('sesiones*') || Request::is('ingresos*') || Request::is('egresos*');
    ?>

    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar glass-panel">
            <div class="logo-container">
                <div class="logo-text">Actúa Iglesia</div>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <span>📊 Dashboard</span>
                    </a>
                </li>
                
                <!-- Acceso y Seguridad -->
                <li class="nav-category">
                    <div class="nav-category-header {{ $accesoActive ? 'expanded' : '' }}" onclick="toggleSubmenu('submenu-acceso', this)">
                        <span>🔐 Acceso y Seguridad</span>
                        <span class="caret">▶</span>
                    </div>
                    <ul class="submenu {{ $accesoActive ? 'expanded' : '' }}" id="submenu-acceso">
                        <li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}"><span>👤 Administrar Usuarios</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('roles') || Request::is('roles/create') || Request::is('roles/*/edit') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}"><span>🔑 Administrar Roles</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('roles/assign*') ? 'active' : '' }}">
                            <a href="{{ route('roles.user') }}"><span>🛡️ Administrar Rol Usuario</span></a>
                        </li>
                    </ul>
                </li>

                <!-- Parametrización -->
                <li class="nav-category">
                    <div class="nav-category-header {{ $paramActive ? 'expanded' : '' }}" onclick="toggleSubmenu('submenu-param', this)">
                        <span>⚙️ Parametrización</span>
                        <span class="caret">▶</span>
                    </div>
                    <ul class="submenu {{ $paramActive ? 'expanded' : '' }}" id="submenu-param">
                        <li class="nav-item {{ Request::is('miembros*') ? 'active' : '' }}">
                            <a href="{{ route('miembros.index') }}"><span>👥 Administrar Miembro</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cultos*') ? 'active' : '' }}">
                            <a href="{{ route('cultos.index') }}"><span>⛪ Administrar Culto</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('actividades*') ? 'active' : '' }}">
                            <a href="{{ route('actividades.index') }}"><span>📅 Administrar Actividad</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cursos*') ? 'active' : '' }}">
                            <a href="{{ route('cursos.index') }}"><span>📖 Administrar Curso</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cargos*') ? 'active' : '' }}">
                            <a href="{{ route('cargos.index') }}"><span>💼 Administrar Cargo</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('lideres*') ? 'active' : '' }}">
                            <a href="{{ route('lideres.index') }}"><span>👔 Administrar Líderes Iglesia</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('ministerios*') ? 'active' : '' }}">
                            <a href="{{ route('ministerios.index') }}"><span>🛡️ Administrar Ministerio</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('docentes*') ? 'active' : '' }}">
                            <a href="{{ route('docentes.index') }}"><span>🎓 Administrar Docentes</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('seguimiento*') ? 'active' : '' }}">
                            <a href="{{ route('seguimiento.index') }}"><span>📈 Administrar Seguimiento</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('contratos*') ? 'active' : '' }}">
                            <a href="{{ route('contratos.index') }}"><span>📝 Administrar Contratos</span></a>
                        </li>
                    </ul>
                </li>

                <!-- Transaccional -->
                <li class="nav-category">
                    <div class="nav-category-header {{ $transacActive ? 'expanded' : '' }}" onclick="toggleSubmenu('submenu-transac', this)">
                        <span>🔄 Transaccional</span>
                        <span class="caret">▶</span>
                    </div>
                    <ul class="submenu {{ $transacActive ? 'expanded' : '' }}" id="submenu-transac">
                        <li class="nav-item {{ Request::is('sesiones*') ? 'active' : '' }}">
                            <a href="{{ route('sesiones.index') }}"><span>⏳ Administrar Sesiones</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('inscripciones*') ? 'active' : '' }}">
                            <a href="{{ route('inscripciones.index') }}"><span>📝 Administrar Inscripciones</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('asistencia*') ? 'active' : '' }}">
                            <a href="{{ route('asistencia.index') }}"><span>📋 Administrar Asistencia</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('ingresos*') ? 'active' : '' }}">
                            <a href="{{ route('ingresos.index') }}"><span>💵 Administrar Ingresos</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('egresos*') ? 'active' : '' }}">
                            <a href="{{ route('egresos.index') }}"><span>💸 Administrar Egresos</span></a>
                        </li>
                    </ul>
                </li>

        <?php
            $reporteActive = Request::is('reportes*');
        ?>
                <li class="nav-category">
                    <div class="nav-category-header {{ $reporteActive ? 'expanded' : '' }}" onclick="toggleSubmenu('submenu-reporte', this)">
                        <span>📊 Reporte</span>
                        <span class="caret">▶</span>
                    </div>
                    <ul class="submenu {{ $reporteActive ? 'expanded' : '' }}" id="submenu-reporte">
                        <li class="nav-item {{ Request::is('reportes*') ? 'active' : '' }}">
                            <a href="{{ route('reportes.index') }}"><span>📊 Administrar Reportes</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>

        <!-- Main Workspace -->
        <main class="main-content">
            <!-- Header -->
            <header class="header glass-panel" style="margin: 15px 15px 0 15px; border-radius: 15px;">
                <div style="display: flex; align-items: center;">
                    <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                    <div class="header-title"></div>
                </div>
                <div class="user-profile">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main view content -->
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}

                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}

                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Sidebar Drawer Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        function toggleSubmenu(id, header) {
            const submenu = document.getElementById(id);
            const isExpanded = submenu.classList.contains('expanded');
            
            if (isExpanded) {
                // Collapsing: set current height first so transition works
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                submenu.offsetHeight; // Force repaint
                
                submenu.classList.remove('expanded');
                header.classList.remove('expanded');
                submenu.style.maxHeight = '0px';
            } else {
                // Expanding: measure height by temporarily setting max-height to none
                submenu.style.maxHeight = 'none';
                const height = submenu.scrollHeight;
                submenu.style.maxHeight = '0px';
                submenu.offsetHeight; // Force repaint
                
                submenu.classList.add('expanded');
                header.classList.add('expanded');
                submenu.style.maxHeight = height + 'px';
            }
        }

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            } else {
                const container = document.querySelector('.app-container');
                container.classList.toggle('sidebar-collapsed');
            }
        }

        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const button = input.nextElementSibling;
            const eyeOpen = button.querySelector('.eye-open');
            const eyeClosed = button.querySelector('.eye-closed');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\proyecto\resources\views/layouts/app.blade.php ENDPATH**/ ?>
