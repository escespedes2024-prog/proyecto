<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Iglesia Actúa</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @php
        $accesoActive = Request::is('users*') || Request::is('roles*');
        $paramActive = Request::is('miembros*') || Request::is('cultos*') || Request::is('actividades*') || Request::is('cursos*') || Request::is('cargos*') || Request::is('lideres*') || Request::is('ministerios*') || Request::is('docentes*') || Request::is('seguimiento*');
        $transacActive = Request::is('inscripciones*') || Request::is('asistencia*') || Request::is('sesiones*') || Request::is('ingresos*');
    @endphp

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
                            <a href="{{ route('users.index') }}"><span>👤 Usuarios</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('roles') || Request::is('roles/create') || Request::is('roles/*/edit') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}"><span>🔑 Roles</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('roles/assign*') ? 'active' : '' }}">
                            <a href="{{ route('roles.user') }}"><span>🛡️ Rol Usuario</span></a>
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
                            <a href="{{ route('miembros.index') }}"><span>👥 Miembro</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cultos*') ? 'active' : '' }}">
                            <a href="{{ route('cultos.index') }}"><span>⛪ Culto</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('actividades*') ? 'active' : '' }}">
                            <a href="{{ route('actividades.index') }}"><span>📅 Actividad</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cursos*') ? 'active' : '' }}">
                            <a href="{{ route('cursos.index') }}"><span>📖 Curso</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('cargos*') ? 'active' : '' }}">
                            <a href="{{ route('cargos.index') }}"><span>💼 Cargo</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('lideres*') ? 'active' : '' }}">
                            <a href="{{ route('lideres.index') }}"><span>👔 Líderes Iglesia</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('ministerios*') ? 'active' : '' }}">
                            <a href="{{ route('ministerios.index') }}"><span>🛡️ Ministerio</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('docentes*') ? 'active' : '' }}">
                            <a href="{{ route('docentes.index') }}"><span>🎓 Docentes</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('seguimiento*') ? 'active' : '' }}">
                            <a href="{{ route('seguimiento.index') }}"><span>📈 Seguimiento</span></a>
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
                            <a href="{{ route('sesiones.index') }}"><span>⏳ Sesiones</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('inscripciones*') ? 'active' : '' }}">
                            <a href="{{ route('inscripciones.index') }}"><span>📝 Inscripciones</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('asistencia*') ? 'active' : '' }}">
                            <a href="{{ route('asistencia.index') }}"><span>📋 Asistencia</span></a>
                        </li>
                        <li class="nav-item {{ Request::is('ingresos*') ? 'active' : '' }}">
                            <a href="{{ route('ingresos.index') }}"><span>💵 Ingresos</span></a>
                        </li>
                    </ul>
                </li>

                <li style="margin: 15px 0 5px 16px; font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); font-weight: 700; letter-spacing: 1px;">
                    Administración y Finanzas
                </li>
                <li class="nav-item {{ Request::is('egresos*') ? 'active' : '' }}">
                    <a href="{{ route('egresos.index') }}"><span>💸 Egresos</span></a>
                </li>
                <li class="nav-item {{ Request::is('contratos*') ? 'active' : '' }}">
                    <a href="{{ route('contratos.index') }}"><span>📝 Contratos</span></a>
                </li>
            </ul>
        </aside>

        <!-- Main Workspace -->
        <main class="main-content">
            <!-- Header -->
            <header class="header glass-panel" style="margin: 15px 15px 0 15px; border-radius: 15px;">
                <div style="display: flex; align-items: center;">
                    <button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                    <div class="header-title">@yield('title')</div>
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
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
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
