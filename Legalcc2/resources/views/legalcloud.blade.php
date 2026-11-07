<!DOCTYPE html>
<html lang="es" class="is-landing" data-api-base="{{ url('/api/v1') }}" data-api-mode="{{ config('legalcloud.api_mode', 'api') }}" data-initial-view="{{ $initialView ?? 'dashboard' }}" data-initial-settings-section="{{ $settingsSection ?? 'profile' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f5ef">
    <title>LegalCloud AI | Gestión documental</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
<div class="landing-gate" data-landing>
    <header class="landing-nav">
        <a class="brand landing-brand" href="#inicio" aria-label="LegalCloud AI, inicio"><span class="brand-mark">lc<span>.</span></span><span class="brand-name">legalcloud<span>AI</span></span></a>
        <nav class="landing-menu" aria-label="Portada">
            <a href="#inicio">Inicio</a>
            <a href="#caracteristicas">Características</a>
            <a href="#beneficios">Beneficios</a>
            <a href="#planes">Planes</a>
        </nav>
        <div class="landing-nav-actions">
            <button class="landing-login" type="button" data-open-login><i data-icon="user"></i>Iniciar sesión</button>
        </div>
    </header>
    <section class="landing-hero" id="inicio" aria-labelledby="landing-title">
        <div class="landing-hero-copy">
            <h1 id="landing-title">LegalCloud <span>AI</span></h1>
            <p class="landing-hero-lead">Gestiona organiza y encuentra tus documentos legales de forma inteligente en un solo espacio. Expedientes y archivos quedan juntos con su estado y su responsable. La búsqueda encuentra el documento que necesitas y el asistente te ayuda a consultarlo.</p>
            <div class="landing-cta">
                <button class="landing-btn landing-btn-solid" type="button" data-open-login><i data-icon="rocket"></i>Comenzar ahora</button>
            </div>
        </div>
        <figure class="landing-hero-visual">
            <img src="{{ asset('images/landing-scales.jpg') }}" alt="Balanza de la justicia sobre un escritorio, junto a libros y una planta." width="1280" height="720">
        </figure>
    </section>
    <section class="landing-block" id="caracteristicas" aria-labelledby="caracteristicas-title">
        <div class="landing-block-head">
            <p class="landing-kicker is-badge"><i data-icon="grid"></i>Características</p>
            <h2 id="caracteristicas-title">Cuatro piezas del espacio de trabajo</h2>
            <p>Cada una cubre una parte del flujo, del archivo a la consulta.</p>
        </div>
        <div class="feature-grid">
            <article class="feature-card">
                <span class="landing-feature-icon"><i data-icon="folder"></i></span>
                <div>
                    <h3>Gestión documental</h3>
                    <p>Expedientes y archivos quedan en el mismo lugar, con su estado y su responsable.</p>
                </div>
            </article>
            <article class="feature-card">
                <span class="landing-feature-icon"><i data-icon="search"></i></span>
                <div>
                    <h3>Búsqueda inteligente</h3>
                    <p>Localiza un documento o un fragmento sin recorrer carpeta por carpeta.</p>
                </div>
            </article>
            <article class="feature-card">
                <span class="landing-feature-icon"><i data-icon="shield"></i></span>
                <div>
                    <h3>Seguridad</h3>
                    <p>Cada persona entra a lo que le corresponde, con registro de lo que hace.</p>
                </div>
            </article>
            <article class="feature-card">
                <span class="landing-feature-icon"><i data-icon="bell"></i></span>
                <div>
                    <h3>Notificaciones</h3>
                    <p>Avisos cuando un archivo se procesa, cambia o queda listo para consultar.</p>
                </div>
            </article>
        </div>
    </section>
    <section class="landing-block landing-block-band" id="beneficios" aria-labelledby="beneficios-title">
        <div class="landing-block-head">
            <p class="landing-kicker is-badge"><i data-icon="sparkles"></i>Beneficios</p>
            <h2 id="beneficios-title">Menos búsqueda suelta, más control</h2>
            <p>El equipo trabaja sobre los mismos expedientes, con una sola fuente.</p>
        </div>
        <ol class="benefit-grid">
            <li>
                <span>01</span>
                <h3>El expediente reunido</h3>
                <p>Documentos, versiones y estado viven juntos. No hay que armar el caso desde el correo.</p>
            </li>
            <li>
                <span>02</span>
                <h3>Consulta con contexto</h3>
                <p>La búsqueda y el asistente se apoyan en lo que ya está cargado en la organización.</p>
            </li>
            <li>
                <span>03</span>
                <h3>Orden para el equipo</h3>
                <p>Roles, auditoría y un espacio compartido, sin mezclar el trabajo de cada persona.</p>
            </li>
        </ol>
    </section>
    <section class="landing-block" id="planes" aria-labelledby="planes-title">
        <div class="landing-block-head">
            <p class="landing-kicker is-badge"><i data-icon="tag"></i>Planes</p>
            <h2 id="planes-title">Elige el plan de tu espacio</h2>
            <p>Empieza sin costo o pasa al plan completo cuando el equipo lo necesite.</p>
        </div>
        <div class="landing-plan-grid">
            <article class="plan-card">
                <span class="plan-badge">Free</span>
                <p class="plan-price"><em>$0</em> <span>/ mes</span></p>
                <ul class="plan-list">
                    <li><i data-icon="check"></i>Resumen y expedientes</li>
                    <li><i data-icon="check"></i>Biblioteca de documentos</li>
                    <li><i data-icon="check"></i>Un espacio de trabajo</li>
                    <li><i data-icon="check"></i>Soporte por correo</li>
                </ul>
                <button class="landing-btn landing-btn-ghost" type="button" data-open-login>Iniciar sesión</button>
            </article>
            <article class="plan-card is-premium">
                <span class="plan-badge">Premium</span>
                <p class="plan-price"><em>$49</em> <span>/ mes</span></p>
                <ul class="plan-list">
                    <li><i data-icon="check"></i>Todo lo de Free</li>
                    <li><i data-icon="check"></i>OCR y procesamiento</li>
                    <li><i data-icon="check"></i>Búsqueda y asistente IA</li>
                    <li><i data-icon="check"></i>Usuarios, roles y auditoría</li>
                </ul>
                <button class="landing-btn landing-btn-solid" type="button" data-open-login>Iniciar sesión</button>
            </article>
        </div>
    </section>
    <footer class="landing-footer" id="contacto">
        <a class="brand landing-brand" href="#inicio" aria-label="LegalCloud AI, inicio"><span class="brand-mark">lc<span>.</span></span><span class="brand-name">legalcloud<span>AI</span></span></a>
        <p>Innovación y tecnología al servicio de la justicia</p>
        <small>© 2026 LegalCloud AI.</small>
    </footer>
</div>
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
        <button class="button button-quiet" type="button" data-back-landing>Volver a la portada</button>
    </form>
</div>
<div class="app-shell" hidden>
    <div class="mobile-scrim" data-scrim></div>
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="/" aria-label="LegalCloud AI, inicio"><span class="brand-mark">lc<span>.</span></span><span class="brand-name">legalcloud<span>AI</span></span></a>
        <div class="workspace-switcher"><span class="workspace-avatar" data-workspace-avatar>N</span><span class="workspace-copy"><strong data-workspace-name>Tu organización</strong><small>Espacio de trabajo</small></span></div>
        <nav class="side-nav" aria-label="Navegación principal">
            <p class="nav-label">ESPACIO DE TRABAJO</p>
            <button class="nav-link is-active" data-nav="dashboard"><i data-icon="grid"></i><span>Resumen</span></button>
            <button class="nav-link" data-nav="cases"><i data-icon="folder"></i><span>Expedientes</span><span class="nav-count" data-case-count>0</span></button>
            <button class="nav-link" data-nav="documents"><i data-icon="file"></i><span>Documentos</span></button>
            <button class="nav-link" data-nav="ocr"><i data-icon="scan"></i><span>Procesamiento</span></button>
            <button class="nav-link" data-nav="search"><i data-icon="search"></i><span>Búsqueda inteligente</span></button>
            <button class="nav-link" data-nav="assistant"><i data-icon="sparkles"></i><span>Asistente IA</span><span class="nav-new">IA</span></button>
            <button class="nav-link" data-nav="notifications"><i data-icon="bell"></i><span>Notificaciones</span></button>
            <p class="nav-label nav-label-spaced">ADMINISTRACIÓN</p>
            <button class="nav-link" data-nav="users"><i data-icon="users"></i><span>Usuarios y roles</span></button>
            <button class="nav-link" data-nav="categories"><i data-icon="folder"></i><span>Categorías</span></button>
            <button class="nav-link" data-nav="audit"><i data-icon="activity"></i><span>Auditoría</span></button>
        </nav>
        <div class="sidebar-bottom">
            <div class="secure-note"><span class="secure-icon"><i data-icon="shield"></i></span><span><strong>Espacio protegido</strong><small>Trazabilidad activa</small></span><span class="secure-dot"></span></div>
            <button class="profile-button" type="button" data-nav="settings"><span class="profile-avatar" data-profile-avatar>LC</span><span class="profile-copy"><strong data-profile-name>Cuenta</strong><small data-profile-role>Sesión</small></span></button>
            <button class="nav-link logout-link" type="button" data-logout><i data-icon="close"></i><span>Cerrar sesión</span></button>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <button class="icon-button menu-toggle" type="button" aria-label="Abrir menú" data-menu-toggle><i data-icon="menu"></i></button>
            <div class="breadcrumbs"><span data-org-label>Tu organización</span><i data-icon="chevron-right"></i><strong data-breadcrumb>Resumen</strong></div>
            <div class="topbar-actions">
                <label class="global-search"><i data-icon="search"></i><input type="search" placeholder="Buscar expedientes o documentos" aria-label="Buscar expedientes o documentos" data-global-search><kbd>/</kbd></label>
                <button class="icon-button notification-button" type="button" aria-label="Notificaciones" title="Notificaciones" data-notifications><i data-icon="bell"></i><span class="notification-dot"></span></button>
                <span class="topbar-divider"></span><span class="today-label" data-today>Hoy</span>
            </div>
            <div class="notification-popover" data-notification-popover hidden>
                <div class="popover-heading"><strong>Notificaciones</strong><span data-notification-count>0</span><button class="button button-quiet" type="button" data-nav="notifications">Ver todas</button></div>
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
<dialog class="modal" id="document-edit-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">BIBLIOTECA DOCUMENTAL</div><h2>Editar documento</h2><p class="modal-description" data-document-label></p>
    <form class="form-stack" data-document-edit-form>
        <input type="hidden" name="documentId">
        <label>Título<input name="title" required maxlength="200"></label>
        <label>Descripción<textarea name="description" rows="3" maxlength="2000" placeholder="Notas o contexto del documento"></textarea></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Guardar cambios</button></div>
    </form>
</dialog>
<dialog class="modal" id="document-version-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">BIBLIOTECA DOCUMENTAL</div><h2>Nueva versión</h2><p class="modal-description">El archivo sustituye la versión actual de <strong data-document-label></strong> y vuelve a extraerse.</p>
    <form class="form-stack" data-document-version-form>
        <input type="hidden" name="documentId">
        <label class="drop-zone"><i data-icon="upload"></i><strong>Selecciona el archivo</strong><small>PDF, DOCX o imagen · máximo 25 MB</small><input type="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.tif,.tiff" required></label>
        <label>Nota del cambio<input name="changeNote" maxlength="1000" placeholder="Qué cambió en esta versión"></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Cargar versión</button></div>
    </form>
</dialog>
<dialog class="modal" id="document-versions-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">BIBLIOTECA DOCUMENTAL</div><h2>Versiones</h2><p class="modal-description" data-document-label></p>
    <div class="version-list" data-version-list></div>
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
<dialog class="modal" id="category-modal">
    <form method="dialog" class="modal-close-row"><button class="icon-button" aria-label="Cerrar"><i data-icon="close"></i></button></form>
    <div class="modal-eyebrow">ORGANIZACIÓN</div><h2>Nueva categoría</h2><p class="modal-description">Sirve para agrupar expedientes y documentos.</p>
    <form class="form-stack" data-category-form>
        <label>Nombre<input name="name" required maxlength="120"></label>
        <label>Descripción<textarea name="description" rows="3" maxlength="1000"></textarea></label>
        <div class="form-actions"><button class="button button-quiet" type="button" data-dismiss>Cancelar</button><button class="button button-primary" type="submit">Crear categoría</button></div>
    </form>
</dialog>
<div class="toast-region" aria-live="polite" data-toast-region></div>
</body>
</html>