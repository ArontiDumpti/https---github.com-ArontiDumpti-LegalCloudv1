<!DOCTYPE html>
<html lang="es" data-api-base="{{ url('/api/v1') }}" data-api-mode="{{ config('legalcloud.api_mode', 'api') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f5ef">
    <title>LegalCloud AI | Gestión documental</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-gate" data-auth-gate hidden>
    <form class="auth-card" data-login-form>
        <span class="brand-mark">lc<span>.</span></span>
        <p class="eyebrow">ACCESO</p>
        <h1>Entra a tu espacio</h1>
        <p>Usa la cuenta de tu organización para consultar expedientes y documentos.</p>
        <label>Correo<input name="email" type="email" autocomplete="username" required></label>
        <label>Contraseña<input name="password" type="password" autocomplete="current-password" required minlength="12"></label>
        <p class="auth-error" data-login-error hidden></p>
        <button class="button button-primary" type="submit">Iniciar sesión</button>
    </form>
</div>
<div class="app-shell">
    <div class="mobile-scrim" data-scrim></div>
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="/" aria-label="LegalCloud AI, inicio"><span class="brand-mark">lc<span>.</span></span><span class="brand-name">legalcloud<span>AI</span></span></a>
        <div class="workspace-switcher"><span class="workspace-avatar" data-workspace-avatar>N</span><span class="workspace-copy"><strong data-workspace-name>Tu organización</strong><small>Espacio de trabajo</small></span><span class="workspace-caret">⌄</span></div>
        <nav class="side-nav" aria-label="Navegación principal">
            <p class="nav-label">ESPACIO DE TRABAJO</p>
            <button class="nav-link is-active" data-nav="dashboard"><i data-icon="grid"></i><span>Resumen</span></button>
            <button class="nav-link" data-nav="cases"><i data-icon="folder"></i><span>Expedientes</span><span class="nav-count" data-case-count>0</span></button>
            <button class="nav-link" data-nav="documents"><i data-icon="file"></i><span>Documentos</span></button>
            <button class="nav-link" data-nav="search"><i data-icon="search"></i><span>Búsqueda inteligente</span></button>
            <button class="nav-link" data-nav="assistant"><i data-icon="sparkles"></i><span>Asistente IA</span><span class="nav-new">IA</span></button>
            <p class="nav-label nav-label-spaced">ADMINISTRACIÓN</p>
            <button class="nav-link" data-nav="users"><i data-icon="users"></i><span>Usuarios y roles</span></button>
            <button class="nav-link" data-nav="audit"><i data-icon="activity"></i><span>Auditoría</span></button>
        </nav>
        <div class="sidebar-bottom">
            <div class="secure-note"><span class="secure-icon"><i data-icon="shield"></i></span><span><strong>Espacio protegido</strong><small>Trazabilidad activa</small></span><span class="secure-dot"></span></div>
            <button class="profile-button" type="button"><span class="profile-avatar" data-profile-avatar>LC</span><span class="profile-copy"><strong data-profile-name>Cuenta</strong><small data-profile-role>Sesión</small></span><i data-icon="more"></i></button>
            <button class="nav-link logout-link" type="button" data-logout><i data-icon="close"></i><span>Cerrar sesión</span></button>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <button class="icon-button menu-toggle" type="button" aria-label="Abrir menú" data-menu-toggle><i data-icon="menu"></i></button>
            <div class="breadcrumbs"><span data-org-label>Tu organización</span><i data-icon="chevron-right"></i><strong data-breadcrumb>Resumen</strong></div>
            <div class="topbar-actions">
                <label class="global-search"><i data-icon="search"></i><input type="search" placeholder="Buscar en LegalCloud" aria-label="Buscar en LegalCloud" data-global-search><kbd>/</kbd></label>
                <button class="icon-button notification-button" type="button" aria-label="Notificaciones" title="Notificaciones" data-notifications><i data-icon="bell"></i><span class="notification-dot"></span></button>
                <span class="topbar-divider"></span><span class="today-label" data-today>Hoy</span>
            </div>
            <div class="notification-popover" data-notification-popover hidden>
                <div class="popover-heading"><strong>Notificaciones</strong><span data-notification-count>0</span></div>
                <div data-notification-list><div class="empty-state">No hay notificaciones.</div></div>
            </div>
        </header>
        <section class="page-content" id="page-content" aria-live="polite"></section>
    </main>
</div>
<dialog class="modal" id="case-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">NUEVO REGISTRO</div><h2>Crear expediente</h2><p class="modal-description">Añade un expediente al espacio documental de tu organización.</p>
    <form class="form-stack" data-case-form>
        <label>Referencia<input name="reference" placeholder="EXP-2026-025" required maxlength="80"></label>
        <label>Nombre del expediente<input name="title" placeholder="Ej. Revisión normativa de contratación" required maxlength="200"></label>
        <label>Descripción<textarea name="description" rows="3" placeholder="Contexto o notas iniciales"></textarea></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Crear expediente <i data-icon="arrow-right"></i></button></div>
    </form>
</dialog>
<dialog class="modal" id="upload-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">BIBLIOTECA DOCUMENTAL</div><h2>Cargar documento</h2><p class="modal-description">El archivo quedará vinculado a un expediente y listo para procesar.</p>
    <form class="form-stack" data-upload-form>
        <label class="drop-zone"><i data-icon="upload"></i><strong>Selecciona un archivo</strong><small>PDF, DOCX o imagen · máximo 25 MB</small><input type="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.tif,.tiff" required></label>
        <label>Expediente<select name="caseId" required data-case-select></select></label>
        <label>Título<input name="title" placeholder="Nombre del documento" required maxlength="200"></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Cargar documento <i data-icon="arrow-up"></i></button></div>
    </form>
</dialog>
<dialog class="modal" id="user-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">ADMINISTRACIÓN</div><h2>Crear usuario</h2><p class="modal-description">La persona podrá entrar con este correo y esta contraseña.</p>
    <form class="form-stack" data-user-form>
        <label>Nombre<input name="name" required minlength="2" maxlength="160"></label>
        <label>Correo<input name="email" type="email" required></label>
        <label>Contraseña temporal<input name="password" type="password" required minlength="12"></label>
        <label>Rol<select name="role" required><option value="consultant">Usuario consultor</option><option value="analyst">Especialista / analista</option><option value="records_manager">Responsable documental</option><option value="administrator">Administrador</option></select></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Crear usuario</button></div>
    </form>
</dialog>
<div class="toast-region" aria-live="polite" data-toast-region></div>
</body>
</html>