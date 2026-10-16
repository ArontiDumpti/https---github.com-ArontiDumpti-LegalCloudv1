const root = document.documentElement;
const page = document.querySelector('#page-content');
const apiBase = root.dataset.apiBase || '/api/v1';
const apiMode = root.dataset.apiMode || 'demo';
const paths = {
	activity: '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>', arrowRight: '<path d="M5 12h14M12 5l7 7-7 7"/>', arrowUp: '<path d="M12 19V5m-7 7 7-7 7 7"/>',
	bell: '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>', calendar: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
	check: '<path d="m5 12 4 4L19 6"/>', chevronRight: '<path d="m9 18 6-6-6-6"/>', close: '<path d="m18 6-12 12M6 6l12 12"/>',
	file: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h6"/>',
	filePlus: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h8M14 2v6h6M8 13h6M19 16v6m-3-3h6"/>', folder: '<path d="M20 20H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2ZM2 10h20"/>',
	grid: '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
	menu: '<path d="M4 6h16M4 12h16M4 18h16"/>', more: '<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>', search: '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
	scan: '<path d="M3 7V5a2 2 0 0 1 2-2h2m10 0h2a2 2 0 0 1 2 2v2m0 10v2a2 2 0 0 1-2 2h-2m-10 0H5a2 2 0 0 1-2-2v-2M3 12h18"/>',
	send: '<path d="m22 2-7 20-4-9-9-4 20-7Zm0 0L11 13"/>', shield: '<path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Zm-3-10 2 2 4-4"/>',
	sparkles: '<path d="m12 3 1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2L12 3Zm7 11 1.2 2.8L23 18l-2.8 1.2L19 22l-1.2-2.8L15 18l2.8-1.2L19 14Z"/>',
	upload: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m14-7-5-5-5 5m5-5v12"/>', users: '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10-3v6m3-3h-6"/>',
};
const icon = (name) => `<svg viewBox="0 0 24 24" aria-hidden="true">${paths[name] || paths.file}</svg>`;
const escape = (value = '') => String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
const cases = [
	{ id: 'case-018', reference: 'EXP-2024-018', title: 'Revisión normativa de contratación pública', status: 'in_review', documents: 18, owner: 'Mariana García', updated: 'Hoy, 10:42' },
	{ id: 'case-011', reference: 'EXP-2024-011', title: 'Procedimiento de licencias ambientales', status: 'open', documents: 32, owner: 'Diego Navarro', updated: 'Hoy, 09:18' },
	{ id: 'case-023', reference: 'EXP-2024-023', title: 'Protección de datos personales', status: 'open', documents: 11, owner: 'Lucía Torres', updated: 'Ayer, 16:35' },
	{ id: 'case-007', reference: 'EXP-2023-007', title: 'Recursos administrativos — Distrito Norte', status: 'closed', documents: 46, owner: 'Mariana García', updated: '14 oct, 12:10' },
	{ id: 'case-025', reference: 'EXP-2026-025', title: 'Actualización de lineamientos internos', status: 'draft', documents: 4, owner: 'Andrés Ríos', updated: '13 oct, 15:24' },
];
const documents = [
	{ id: 'doc-1', title: 'Resolución 118/2024 — Requisitos de contratación', caseId: 'case-018', caseName: 'Contratación pública', type: 'PDF', status: 'indexed', date: '16 oct 2026', size: '2.4 MB', owner: 'Mariana García' },
	{ id: 'doc-2', title: 'Reglamento de compras y suministros', caseId: 'case-018', caseName: 'Contratación pública', type: 'PDF', status: 'indexed', date: '15 oct 2026', size: '1.8 MB', owner: 'Diego Navarro' },
	{ id: 'doc-3', title: 'Informe técnico de impacto ambiental', caseId: 'case-011', caseName: 'Licencias ambientales', type: 'DOCX', status: 'processing', date: '15 oct 2026', size: '856 KB', owner: 'Lucía Torres' },
	{ id: 'doc-4', title: 'Circular 07 — Protección de datos', caseId: 'case-023', caseName: 'Protección de datos', type: 'PDF', status: 'indexed', date: '14 oct 2026', size: '640 KB', owner: 'Mariana García' },
	{ id: 'doc-5', title: 'Anexo II — Matriz de evaluación', caseId: 'case-018', caseName: 'Contratación pública', type: 'XLS', status: 'review', date: '14 oct 2026', size: '312 KB', owner: 'Andrés Ríos' },
	{ id: 'doc-6', title: 'Acta de resolución de expediente', caseId: 'case-007', caseName: 'Recursos administrativos', type: 'PDF', status: 'indexed', date: '12 oct 2026', size: '1.1 MB', owner: 'Diego Navarro' },
];
const users = [
	{ initials: 'MG', name: 'Mariana García', email: 'mariana@navarro.legal', role: 'Administradora', status: 'Activo' },
	{ initials: 'DN', name: 'Diego Navarro', email: 'diego@navarro.legal', role: 'Responsable documental', status: 'Activo' },
	{ initials: 'LT', name: 'Lucía Torres', email: 'lucia@navarro.legal', role: 'Especialista / analista', status: 'Activo' },
	{ initials: 'AR', name: 'Andrés Ríos', email: 'andres@navarro.legal', role: 'Usuario consultor', status: 'Invitado' },
];
const state = { view: 'dashboard', filter: 'all', query: '', searchHits: null, conversationId: null, chat: [] };
const names = { dashboard: 'Resumen', cases: 'Expedientes', documents: 'Documentos', search: 'Búsqueda inteligente', assistant: 'Asistente IA', users: 'Usuarios y roles', audit: 'Auditoría' };
const statusName = { indexed: 'Indexado', uploaded: 'Cargado', processing: 'Procesando', review: 'En revisión', open: 'Abierto', in_review: 'En revisión', closed: 'Cerrado', draft: 'Borrador', failed: 'Error', archived: 'Archivado' };
const pill = (status) => `<span class="status-pill ${escape(status)}">${escape(statusName[status] || status)}</span>`;
const initials = (name) => name.split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase();

function drawIcons(scope = document) {
	scope.querySelectorAll('[data-icon]').forEach((node) => {
		const name = node.dataset.icon.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
		if (paths[name]) node.innerHTML = icon(name);
	});
}
async function request(path, options = {}) {
	const response = await fetch(`${apiBase}${path}`, { ...options, headers: { Accept: 'application/json', ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }), ...options.headers } });
	if (!response.ok) throw new Error(`API ${response.status}`);
	return response.status === 204 ? null : response.json();
}
async function loadApiCollections() {
	if (apiMode === 'demo') return;
	try {
		const [caseResponse, documentResponse, userResponse] = await Promise.all([
			request('/cases?perPage=100'), request('/documents?perPage=100'), request('/users?perPage=100'),
		]);
		cases.splice(0, cases.length, ...caseResponse.data.map((item) => ({
			...item, documents: item.documentCount, owner: item.ownerId || 'Equipo legal', updated: item.updatedAt || '—',
		})));
		documents.splice(0, documents.length, ...documentResponse.data.map((item) => ({
			...item,
			caseName: cases.find((caseItem) => caseItem.id === item.caseId)?.title || 'Expediente',
			type: item.mimeType?.includes('word') ? 'DOCX' : item.mimeType?.includes('spreadsheet') ? 'XLS' : 'PDF',
			date: item.createdAt || '—',
			size: item.sizeBytes ? `${Math.round(item.sizeBytes / 1024)} KB` : '—',
		})));
		users.splice(0, users.length, ...userResponse.data.map((item) => ({
			initials: initials(item.name), name: item.name, email: item.email,
			role: ({ administrator: 'Administradora', records_manager: 'Responsable documental', analyst: 'Especialista / analista', consultant: 'Usuario consultor' })[item.role] || item.role,
			status: item.status === 'active' ? 'Activo' : item.status === 'invited' ? 'Invitado' : 'Suspendido',
		})));
	} catch {
		cases.length = 0;
		documents.length = 0;
		users.length = 0;
		toast('No se pudo conectar con el API. Revisa la configuración del backend.');
	}
	render();
}
function heading(eyebrow, title, subtitle, actions = '') { return `<div class="page-heading"><div><p class="eyebrow">${eyebrow}</p><h1>${title}</h1><p>${subtitle}</p></div><div class="heading-actions">${actions}</div></div>`; }
const actions = () => `<button class="button button-quiet" data-action="upload">${icon('upload')}<span>Cargar documento</span></button><button class="button button-primary" data-action="new-case">${icon('filePlus')}<span>Nuevo expediente</span></button>`;
function rows(list) {
	if (!list.length) return '<tr><td colspan="5"><div class="empty-state">No hay documentos para mostrar.</div></td></tr>';
	return list.map((doc) => `<tr><td><div class="file-cell"><span class="file-type ${doc.type === 'DOCX' ? 'doc' : doc.type === 'XLS' ? 'img' : ''}">${escape(doc.type)}</span><span class="file-copy"><strong>${escape(doc.title)}</strong><small>${escape(doc.caseName || doc.caseId)}</small></span></div></td><td>${pill(doc.status)}</td><td>${escape(doc.date || 'Hoy')}</td><td>${escape(doc.size || '—')}</td><td><button class="icon-button" aria-label="Más opciones">${icon('more')}</button></td></tr>`).join('');
}
function dashboard() {
	page.innerHTML = `${heading('JUEVES, 16 DE OCTUBRE DE 2026', 'Buenos días, Mariana', 'Aquí tienes el pulso documental de tu organización.', actions())}
	<section class="kpi-grid"><article class="kpi"><div class="kpi-label">Expedientes activos<span class="kpi-icon">${icon('folder')}</span></div><div class="kpi-value">24 <span class="kpi-trend">↗ 12%</span></div><div class="kpi-caption">vs. mes anterior</div></article><article class="kpi"><div class="kpi-label">Documentos indexados<span class="kpi-icon amber">${icon('file')}</span></div><div class="kpi-value">1.284 <span class="kpi-trend">↗ 8,4%</span></div><div class="kpi-caption">92 documentos este mes</div></article><article class="kpi"><div class="kpi-label">Procesos OCR<span class="kpi-icon coral">${icon('scan')}</span></div><div class="kpi-value">07 <span class="kpi-trend down">2 pendientes</span></div><div class="kpi-caption">tiempo medio: 34 segundos</div></article><article class="kpi"><div class="kpi-label">Consultas a Legal AI<span class="kpi-icon">${icon('sparkles')}</span></div><div class="kpi-value">186 <span class="kpi-trend">↗ 18%</span></div><div class="kpi-caption">esta semana</div></article></section>
	<div class="dashboard-grid"><div class="content-column">
		<section class="panel activity-panel"><div class="panel-heading"><div><h2>Actividad documental</h2><p>Documentos procesados durante los últimos 7 días</p></div><button class="panel-link" data-nav="documents">Ver documentos ${icon('arrowRight')}</button></div><div class="activity-chart" role="img" aria-label="Actividad documental de la semana">${[['Lun',36,22],['Mar',62,34],['Mié',47,58],['Jue',76,45],['Vie',54,30],['Sáb',23,14],['Dom',18,11]].map(([day,a,b])=>`<div class="chart-day"><div class="chart-bars"><span class="chart-bar" style="height:${a}%"></span><span class="chart-bar alt" style="height:${b}%"></span></div>${day}</div>`).join('')}</div><div class="chart-legend"><span class="legend-item"><span class="legend-dot"></span>Documentos cargados</span><span class="legend-item"><span class="legend-dot alt"></span>Procesos OCR</span></div></section>
		<section class="panel"><div class="panel-heading"><div><h2>Documentos recientes</h2><p>Últimos archivos añadidos a tu organización</p></div><button class="panel-link" data-nav="documents">Ver biblioteca ${icon('arrowRight')}</button></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Documento</th><th>Estado</th><th>Fecha</th><th>Tamaño</th><th></th></tr></thead><tbody>${rows(documents.slice(0,4))}</tbody></table></div></section>
		<section class="panel"><div class="panel-heading"><div><h2>Actividad reciente</h2><p>Operaciones registradas en tu espacio</p></div><button class="panel-link" data-nav="audit">Ver auditoría ${icon('arrowRight')}</button></div><div class="activity-list"><div class="activity-item"><span class="activity-marker">${icon('upload')}</span><div class="activity-copy"><p><strong>Mariana García</strong> cargó Resolución 118/2024</p><small>Expediente EXP-2024-018 · hace 12 min</small></div></div><div class="activity-item"><span class="activity-marker">${icon('sparkles')}</span><div class="activity-copy"><p><strong>Lucía Torres</strong> consultó al asistente Legal AI</p><small>3 fuentes citadas · hace 48 min</small></div></div><div class="activity-item"><span class="activity-marker">${icon('check')}</span><div class="activity-copy"><p><strong>Diego Navarro</strong> cerró la revisión documental</p><small>Expediente EXP-2024-011 · hace 2 h</small></div></div></div></section>
	</div><div class="insight-column">
		<section class="panel assistant-panel"><div class="panel-heading"><div class="assistant-title"><span class="assistant-orb">${icon('sparkles')}</span><div><h2>Pregunta a Legal AI</h2><p>Respuestas con fuentes verificables</p></div></div><span class="assistant-tag">RAG</span></div><div class="assistant-body"><p class="assistant-greeting">Tu documentación, en conversación.</p><p class="assistant-hint">Consulta normas y expedientes. Cada respuesta incluye las fuentes que la sustentan.</p><button class="prompt-suggestion" data-prompt="¿Qué requisitos establece la Resolución 118 para contratar?">${icon('file')}¿Qué requisitos establece la Resolución 118?</button><button class="prompt-suggestion" data-prompt="Resume los cambios recientes en el expediente de licencias">${icon('activity')}Resume los cambios recientes del expediente</button><form class="assistant-input-wrap" data-quick-ask><input name="question" aria-label="Pregunta a Legal AI" placeholder="Escribe una pregunta..." required><button class="send-button" aria-label="Enviar pregunta">${icon('send')}</button></form><p class="assistant-footnote">${icon('shield')}Solo consulta documentos con acceso autorizado</p></div></section>
		<section class="panel"><div class="panel-heading"><div><h2>Procesamiento</h2><p>Estado de la cola documental</p></div><button class="icon-button" aria-label="Ver documentos" data-nav="documents">${icon('arrowRight')}</button></div><div class="processing-row"><span class="processing-symbol">${icon('scan')}</span><span class="processing-copy"><strong>Extracción OCR</strong><small>2 documentos en cola</small></span><span class="processing-count">02</span></div><div class="processing-row"><span class="processing-symbol">${icon('sparkles')}</span><span class="processing-copy"><strong>Indexación semántica</strong><small>Todo al día</small></span><span class="processing-count">00</span></div></section>
	</div></div>`;
}
function renderCases() {
	const list = cases.filter((item) => `${item.title} ${item.reference} ${item.owner}`.toLowerCase().includes(state.query.toLowerCase()) && (state.filter === 'all' || item.status === state.filter));
	page.innerHTML = `${heading('GESTIÓN DOCUMENTAL','Expedientes','Organiza los asuntos y consulta su estado documental.',`<button class="button button-primary" data-action="new-case">${icon('filePlus')}<span>Nuevo expediente</span></button>`)}<div class="list-toolbar"><label class="list-search">${icon('search')}<input type="search" placeholder="Buscar expediente o referencia" aria-label="Buscar expediente" data-list-search value="${escape(state.query)}"></label><select class="filter-select" aria-label="Filtrar por estado" data-status-filter><option value="all">Todos los estados</option><option value="open">Abierto</option><option value="in_review">En revisión</option><option value="draft">Borrador</option><option value="closed">Cerrado</option></select></div><section class="panel list-panel"><div class="table-wrap"><table class="data-table"><thead><tr><th>Expediente</th><th>Estado</th><th>Documentos</th><th>Responsable</th><th>Última actividad</th><th></th></tr></thead><tbody>${list.length?list.map((item)=>`<tr><td><div class="file-copy"><strong>${escape(item.title)}</strong><small class="case-reference">${escape(item.reference)}</small></div></td><td>${pill(item.status)}</td><td>${item.documents}</td><td><span class="owner-chip"><span class="owner-avatar">${initials(item.owner)}</span>${escape(item.owner)}</span></td><td>${escape(item.updated)}</td><td><button class="icon-button" aria-label="Más opciones">${icon('more')}</button></td></tr>`).join(''):'<tr><td colspan="6"><div class="empty-state">No se encontraron expedientes.</div></td></tr>'}</tbody></table></div></section>`;
	page.querySelector('[data-status-filter]').value = state.filter;
}
function renderDocuments() {
	const list = documents.filter((doc) => `${doc.title} ${doc.caseName} ${doc.owner}`.toLowerCase().includes(state.query.toLowerCase()) && (state.filter === 'all' || doc.status === state.filter));
	page.innerHTML = `${heading('BIBLIOTECA','Documentos','Versiones, metadatos y procesamiento de toda tu organización.',`<button class="button button-primary" data-action="upload">${icon('upload')}<span>Cargar documento</span></button>`)}<div class="list-toolbar"><label class="list-search">${icon('search')}<input type="search" placeholder="Buscar por nombre o expediente" aria-label="Buscar documentos" data-list-search value="${escape(state.query)}"></label><select class="filter-select" aria-label="Filtrar documentos" data-status-filter><option value="all">Todos los estados</option><option value="indexed">Indexado</option><option value="processing">Procesando</option><option value="review">En revisión</option></select></div><section class="panel list-panel"><div class="table-wrap"><table class="data-table"><thead><tr><th>Documento</th><th>Estado</th><th>Fecha</th><th>Tamaño</th><th></th></tr></thead><tbody>${rows(list)}</tbody></table></div></section>`;
	page.querySelector('[data-status-filter]').value = state.filter;
}
function renderSearch() {
	const query = state.query.trim();
	const results = state.searchHits || documents.filter((doc) => !query || `${doc.title} ${doc.caseName}`.toLowerCase().includes(query.toLowerCase()) || ['doc-1','doc-2'].includes(doc.id)).slice(0,4);
	page.innerHTML = `${heading('CONSULTA TRANSVERSAL','Encuentra lo importante','Busca en el contenido y los metadatos de tus documentos.')}<form class="search-hero" data-search-form>${icon('search')}<input name="query" type="search" placeholder="Ej. requisitos para contratar servicios públicos" aria-label="Consulta de búsqueda" value="${escape(query)}"><button class="button button-primary" type="submit">${icon('search')}<span>Buscar</span></button></form><div class="search-filters"><span class="filter-chip">${icon('folder')}Todos los expedientes</span><span class="filter-chip">${icon('sparkles')}Búsqueda semántica</span><span class="filter-chip">${icon('calendar')}Últimos 12 meses</span></div><div class="search-results"><p class="eyebrow">${query?`${results.length} RESULTADOS RELEVANTES`:'RESULTADOS SUGERIDOS'}</p>${results.map((doc,index)=>`<article class="search-result"><div class="result-meta"><span class="file-type">${escape(doc.type)}</span><span>${escape(doc.caseName || 'Documento')}</span><span>·</span><span>Versión ${doc.currentVersion?.versionNumber || (index?1:3)}</span></div><h3>${escape(doc.title)}</h3><p>${escape(doc.excerpt || (index?'La documentación aplicable establece las condiciones, plazos y requisitos que deben verificarse antes de iniciar el procedimiento administrativo correspondiente.':'Para iniciar un procedimiento de contratación, la unidad requirente deberá acreditar la disponibilidad presupuestaria, definir los criterios de evaluación y publicar la convocatoria conforme a los plazos establecidos en el reglamento.'))}</p><div class="result-footer">${icon('file')}Página ${doc.page || index+4}<span>·</span>Relevancia ${doc.score ? Math.round(doc.score*100) : 96-index*7}%</div></article>`).join('')}</div>`;
}
function renderAssistant() {
	page.innerHTML = `${heading('ASISTENTE DOCUMENTAL','Legal AI','Respuestas fundamentadas en las fuentes de tu organización.')}<div class="chat-layout"><section class="panel chat-panel"><div class="chat-heading"><div class="chat-heading-copy"><span class="assistant-orb">${icon('sparkles')}</span><div><h2>Consulta documental</h2><small>Fuentes autorizadas · respuestas trazables</small></div></div><button class="button button-quiet" data-chat-clear>Nueva consulta</button></div><div class="chat-history" data-chat-history>${state.chat.length?state.chat.map((message)=>`<div class="chat-message ${message.role}"><p>${escape(message.text)}</p>${message.citation?`<div class="citation-card">${icon('file')}<span><strong>${escape(message.citation.title)}</strong>${escape(message.citation.detail)}</span></div>`:''}</div>`).join(''):`<div class="chat-empty"><span class="assistant-orb">${icon('sparkles')}</span><h3>¿Qué necesitas encontrar?</h3><p>Pregunta sobre requisitos, compara versiones o resume un expediente. Legal AI responderá con citas a los documentos consultados.</p></div>`}</div><form class="chat-composer" data-chat-form><textarea name="question" rows="2" aria-label="Tu pregunta" placeholder="Pregunta sobre tus documentos..." required></textarea><button class="send-button" aria-label="Enviar consulta">${icon('send')}</button></form></section><aside class="panel chat-sources"><h3>Fuentes consultables</h3><p>El asistente busca solo en documentos a los que tienes acceso.</p><div class="source-item">${icon('file')}<span><strong>1.284 documentos indexados</strong><small>En 24 expedientes activos</small></span></div><div class="source-item">${icon('shield')}<span><strong>Acceso por organización</strong><small>Permisos respetados en cada consulta</small></span></div></aside></div>`;
}
function renderUsers() {
	page.innerHTML = `${heading('ADMINISTRACIÓN','Usuarios y roles','Controla quién accede a los expedientes de tu organización.',`<button class="button button-primary" data-action="invite-user">${icon('users')}<span>Invitar usuario</span></button>`)}<section class="panel list-panel"><div class="table-wrap"><table class="data-table"><thead><tr><th>Usuario</th><th>Rol</th><th>Estado</th><th></th></tr></thead><tbody>${users.map((user)=>`<tr><td><span class="owner-chip"><span class="owner-avatar">${user.initials}</span><span class="file-copy"><strong>${escape(user.name)}</strong><small>${escape(user.email)}</small></span></span></td><td><span class="user-table-role">${escape(user.role)}</span></td><td>${pill(user.status==='Activo'?'indexed':'review')}</td><td><button class="icon-button" aria-label="Más opciones">${icon('more')}</button></td></tr>`).join('')}</tbody></table></div></section>`;
}
function renderAudit() {
	page.innerHTML = `${heading('TRAZABILIDAD','Registro de auditoría','Historial de operaciones y consultas realizadas en la plataforma.')}<section class="panel"><div class="panel-heading"><div><h2>Actividad reciente</h2><p>Los eventos de auditoría son inmutables.</p></div><button class="button button-quiet">${icon('upload')}<span>Exportar registro</span></button></div><div class="activity-list"><div class="activity-item"><span class="activity-marker">${icon('upload')}</span><div class="activity-copy"><p><strong>Mariana García</strong> cargó Resolución 118/2024 — Requisitos de contratación</p><small>document.uploaded · EXP-2024-018 · 16 oct 2026, 10:42</small></div></div><div class="activity-item"><span class="activity-marker">${icon('sparkles')}</span><div class="activity-copy"><p><strong>Lucía Torres</strong> consultó: “¿Qué requisitos establece la resolución para contratar?”</p><small>assistant.query · 3 fuentes consultadas · 16 oct 2026, 10:06</small></div></div><div class="activity-item"><span class="activity-marker">${icon('check')}</span><div class="activity-copy"><p><strong>Diego Navarro</strong> cambió el estado de un expediente a En revisión</p><small>case.status_changed · EXP-2024-011 · 16 oct 2026, 09:18</small></div></div></div></section>`;
}
function render() {
	document.querySelector('[data-breadcrumb]').textContent = names[state.view];
	document.querySelectorAll('[data-nav]').forEach((button) => button.classList.toggle('is-active', button.dataset.nav === state.view));
	({ dashboard, cases: renderCases, documents: renderDocuments, search: renderSearch, assistant: renderAssistant, users: renderUsers, audit: renderAudit }[state.view] || dashboard)();
	drawIcons(page);
}
function toast(message) {
	const item = document.createElement('div'); item.className = 'toast'; item.innerHTML = `${icon('check')}<span>${escape(message)}</span>`;
	document.querySelector('[data-toast-region]').append(item); window.setTimeout(() => item.remove(), 3300);
}
function changeView(view) {
	if (!names[view]) return;
	state.view = view; state.query = ''; state.filter = 'all'; state.searchHits = null;
	document.querySelector('#sidebar').classList.remove('is-open'); document.querySelector('[data-scrim]').classList.remove('is-visible');
	render(); window.scrollTo({ top: 0, behavior: 'smooth' });
}
function openDialog(id) {
	if (id === 'upload-modal') document.querySelector('[data-case-select]').innerHTML = cases.map((item)=>`<option value="${escape(item.id)}">${escape(item.reference)} · ${escape(item.title)}</option>`).join('');
	document.getElementById(id).showModal(); drawIcons(document.getElementById(id));
}
async function ask(question) {
	const clean = question.trim(); if (!clean) return;
	state.chat.push({ role: 'user', text: clean }, { role: 'assistant', text: 'Estoy revisando la documentación pertinente…' }); render();
	if (apiMode !== 'demo') {
		try {
			if (!state.conversationId) {
				const conversation = await request('/assistant/conversations', { method: 'POST', body: JSON.stringify({ title: clean.slice(0,80) }) });
				state.conversationId = conversation.data?.id || conversation.id;
			}
			const answer = await request(`/assistant/conversations/${state.conversationId}/messages`, { method: 'POST', body: JSON.stringify({ question: clean }) });
			state.chat[state.chat.length-1] = { role:'assistant', text:answer.content, citation:answer.citations?.[0]?{title:answer.citations[0].title,detail:answer.citations[0].excerpt}:null };
		} catch { state.chat[state.chat.length-1].text='La consulta no pudo completarse. Verifica que el backend y el servicio RAG estén disponibles.'; }
		render(); return;
	}
	window.setTimeout(()=>{state.chat[state.chat.length-1]={role:'assistant',text:'La Resolución 118/2024 establece que la unidad requirente debe acreditar la disponibilidad presupuestaria, definir los criterios de evaluación antes de publicar la convocatoria y conservar constancia de las ofertas recibidas. El reglamento también exige motivar la selección de la propuesta más conveniente.',citation:{title:'Resolución 118/2024 · Artículos 8, 12 y 19',detail:'Páginas 4–6 · versión 3 · Relevancia 96%'}};render();},650);
}
document.addEventListener('click',(event)=>{
	const nav=event.target.closest('[data-nav]');if(nav)return changeView(nav.dataset.nav);
	const action=event.target.closest('[data-action]')?.dataset.action;
	if(action==='new-case')return openDialog('case-modal');if(action==='upload')return openDialog('upload-modal');if(action==='invite-user')return toast('La invitación de usuarios estará disponible al conectar el backend.');
	if(event.target.closest('[data-dismiss]'))event.target.closest('dialog').close();
	if(event.target.closest('[data-menu-toggle]')){document.querySelector('#sidebar').classList.toggle('is-open');document.querySelector('[data-scrim]').classList.toggle('is-visible');}
	if(event.target.closest('[data-scrim]')){document.querySelector('#sidebar').classList.remove('is-open');document.querySelector('[data-scrim]').classList.remove('is-visible');}
	if(event.target.closest('[data-notifications]')){const pop=document.querySelector('[data-notification-popover]');pop.hidden=!pop.hidden;}
	const suggestion=event.target.closest('[data-prompt]');if(suggestion){changeView('assistant');ask(suggestion.dataset.prompt);}
	if(event.target.closest('[data-chat-clear]')){state.chat=[];state.conversationId=null;render();}
});
document.addEventListener('input',(event)=>{
	if(event.target.matches('[data-list-search]')){state.query=event.target.value;const position=event.target.selectionStart;render();const input=page.querySelector('[data-list-search]');input?.focus();input?.setSelectionRange(position,position);}
	if(event.target.matches('[data-upload-form] input[type=file]')){const file=event.target.files[0];const title=document.querySelector('[data-upload-form] [name=title]');if(file&&!title.value)title.value=file.name.replace(/\.[^.]+$/,'');}
});
document.addEventListener('change',(event)=>{if(event.target.matches('[data-status-filter]')){state.filter=event.target.value;render();}});
document.addEventListener('submit',async(event)=>{
	if(event.target.matches('[data-case-form]')){
		event.preventDefault();const form=event.target,payload=Object.fromEntries(new FormData(form));
		if(apiMode!=='demo')try{await request('/cases',{method:'POST',body:JSON.stringify(payload)});}catch{return toast('No se pudo crear el expediente. Revisa los datos y el backend.');}
		cases.unshift({...payload,id:`case-${Date.now()}`,status:'draft',documents:0,owner:'Mariana García',updated:'Ahora'});form.closest('dialog').close();form.reset();toast('Expediente creado correctamente.');if(state.view==='cases')render();
	}
	if(event.target.matches('[data-upload-form]')){
		event.preventDefault();const form=event.target,data=new FormData(form),file=data.get('file');if(file.size>25*1024*1024)return toast('El archivo supera el límite de 25 MB.');
		if(apiMode!=='demo')try{await request('/documents',{method:'POST',body:data});}catch{return toast('No se pudo cargar el documento. Revisa los datos y el backend.');}
		const related=cases.find((item)=>item.id===data.get('caseId'));documents.unshift({id:`doc-${Date.now()}`,title:data.get('title'),caseId:data.get('caseId'),caseName:related?.title||'Expediente',type:file.name.split('.').pop().toUpperCase(),status:'processing',date:'Ahora',size:`${Math.max(1,Math.round(file.size/1024))} KB`,owner:'Mariana García'});form.closest('dialog').close();form.reset();toast('Documento cargado. El OCR comenzará en breve.');if(['documents','dashboard'].includes(state.view))render();
	}
	if(event.target.matches('[data-search-form]')){
		event.preventDefault();state.query=new FormData(event.target).get('query')||'';state.searchHits=null;
		if(apiMode!=='demo')try{
			const response=await request('/search',{method:'POST',body:JSON.stringify({query:state.query,mode:'hybrid'})});
			state.searchHits=response.data.map((hit)=>({...hit.document,caseName:cases.find((item)=>item.id===hit.document.caseId)?.title,excerpt:hit.excerpt,page:hit.page,score:hit.score}));
		}catch{state.searchHits=[];toast('No se pudo completar la búsqueda. Revisa el servicio de indexación.');}
		render();
	}
	if(event.target.matches('[data-quick-ask],[data-chat-form]')){event.preventDefault();const question=new FormData(event.target).get('question');if(event.target.matches('[data-quick-ask]'))changeView('assistant');ask(question);}
});
document.querySelector('[data-global-search]').addEventListener('keydown',(event)=>{if(event.key==='Enter'){const query=event.currentTarget.value;changeView('search');state.query=query;render();}});
document.addEventListener('keydown',(event)=>{if(event.key==='/'&&!['INPUT','TEXTAREA'].includes(document.activeElement.tagName)){event.preventDefault();document.querySelector('[data-global-search]').focus();}if(event.key==='Escape')document.querySelector('[data-notification-popover]').hidden=true;});
if(apiMode!=='demo'){cases.length=0;documents.length=0;users.length=0;}
drawIcons();render();loadApiCollections();
