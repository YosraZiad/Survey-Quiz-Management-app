<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Survey Responses</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/responses.css') }}">
</head>
<body>
	<div class="app">
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title">Academic Platform</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item active" href="{{ url('/responses') }}">👁️ <span>View Responses</span></a>
				<a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a>
			</nav>
		</aside>

		<main class="content">
			<header class="page-header">
				<h1>Survey Responses</h1>
				<p class="subtitle">View and analyze survey responses with improved readability</p>
				<div class="actions">
					<button class="btn" id="exportBtn">⬇️ Export</button>
				</div>
			</header>

			<section class="toolbar">
				<div class="search">
					<input type="search" placeholder="Search by name or email..." />
				</div>
				<div class="toggles" style="display:flex; gap:8px; align-items:center;">
					<select id="surveySelect" title="Select survey" style="min-width:240px;"></select>
					<span id="typeBadge" class="badge" style="display:none;">type</span>
					<select>
						<option>Medium Font</option>
						<option>Large Font</option>
					</select>
					<label class="switch">
						<input type="checkbox" />
						<span>Summary View</span>
					</label>
				</div>
			</section>

			<section class="stats" id="stats">
				<div class="stat"><div class="num" id="totalResponses">0</div><div class="lbl">Total Responses</div></div>
				<div class="stat"><div class="num" id="avgScore">0</div><div class="lbl">Average Score/Weight</div></div>
				<div class="stat"><div class="num" id="recent">0</div><div class="lbl">Recent (24h)</div></div>
				<div class="stat"><div class="num" id="completion">0%</div><div class="lbl">Completion Rate</div></div>
			</section>

			<section class="list" id="respList"></section>
		</main>
	</div>

	<script>
	// Fetch and render responses for latest or selected survey
	let currentSurveyId = null;
	let surveysCache = [];
	async function init() {
		const params = new URLSearchParams(location.search);
		const qsId = params.get('survey');
		const sRes = await fetch('/api/surveys');
		const sJson = await sRes.json();
		surveysCache = sJson.data || [];
		populateSurveySelect(surveysCache, qsId);
		currentSurveyId = qsId || (surveysCache.find?.(x=>x.is_published)?.id || surveysCache[0]?.id || null);
		if (!currentSurveyId) { document.getElementById('respList').innerHTML = '<div class="row"><div>No surveys found.</div></div>'; return; }
		updateTypeBadge();
		await loadResponses();
	}

	function populateSurveySelect(list, selectedId){
		const sel = document.getElementById('surveySelect');
		if(!sel) return;
		sel.innerHTML = '';
		list.forEach(s => {
			const opt = document.createElement('option');
			opt.value = s.id;
			opt.textContent = `${s.title} (${s.type})`;
			if(String(selectedId||'') === String(s.id)) opt.selected = true;
			sel.appendChild(opt);
		});
		sel.addEventListener('change', () => {
			currentSurveyId = sel.value;
			updateTypeBadge();
			loadResponses();
		});
	}

	function updateTypeBadge(){
		const badge = document.getElementById('typeBadge');
		const s = surveysCache.find(x=> String(x.id) === String(currentSurveyId));
		if (s && badge){
			badge.style.display = 'inline-block';
			badge.textContent = s.type === 'quiz' ? 'Quiz' : 'Survey';
			badge.style.background = s.type === 'quiz' ? '#fde68a' : '#dbeafe';
			badge.style.color = s.type === 'quiz' ? '#92400e' : '#1e40af';
		}
	}

	async function fetchAllResponses(baseUrl){
		let url = baseUrl;
		let all = [];
		let firstPayload = null;
		for(let i=0;i<50;i++){
			const res = await fetch(url);
			const json = await res.json();
			if(!firstPayload) firstPayload = json;
			const pageItems = (json.data && json.data.data) ? json.data.data : (json.data || []);
			all = all.concat(pageItems);
			const next = json.data && (json.data.next_page_url || json.data.links?.next || null);
			if(!next) break;
			url = next;
		}
		return { all, firstPayload };
	}

	async function loadResponses(pageUrl){
		const base = pageUrl || `/api/surveys/${currentSurveyId}/responses`;
		const { all, firstPayload } = await fetchAllResponses(base);
		const stats = (firstPayload && firstPayload.stats) || {}; const data = all;
		document.getElementById('totalResponses').textContent = stats.total ?? data.length;
		document.getElementById('avgScore').textContent = stats.avg_score ?? 0;
		document.getElementById('recent').textContent = stats.recent_24h ?? 0;
		document.getElementById('completion').textContent = (stats.completion_rate ?? 0) + '%';
		const list = document.getElementById('respList'); list.innerHTML = '';
		data.forEach(r=>{
			const row = document.createElement('div'); row.className='row';
			const name = document.createElement('div'); name.className='name'; name.textContent = r.respondent?.name || 'Anonymous';
			const meta = document.createElement('div'); meta.className='meta'; meta.textContent = r.respondent?.email || '';
			const when = document.createElement('div'); when.className='when'; when.textContent = new Date(r.created_at).toLocaleString();
			const btn = document.createElement('button'); btn.className='icon-btn'; btn.textContent='👁️'; btn.title='View';
			btn.addEventListener('click', ()=> { window.location.href = `/responses/${currentSurveyId}`; });
			row.appendChild(name); row.appendChild(meta); row.appendChild(when); row.appendChild(btn);
			list.appendChild(row);
		});
		if (data.length === 0) { list.innerHTML = '<div class="row"><div class="name">No responses yet.</div></div>'; }
	}

	document.getElementById('exportBtn')?.addEventListener('click', ()=>{
		if(!currentSurveyId) return;
		window.location.href = `/api/surveys/${currentSurveyId}/responses?export=csv`;
	});

	init();
	</script>
</body>
</html>

