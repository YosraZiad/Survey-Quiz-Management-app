<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Response Detail</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
	<style>
		.app { grid-template-columns: 260px 1fr; }
		.builder { padding: 0; }
		.page { padding: 22px; }
		.q { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:12px; }
		.q-title { font-weight:700; margin-bottom:8px; }
		.q-grid { display:grid; grid-template-columns: 1fr 320px; gap:16px; }
		.chart { background:#f9fafb; border:1px dashed #e5e7eb; border-radius:10px; padding:12px; min-height:120px; display:flex; align-items:center; justify-content:center; color:#6b7280; }
		.stat { font-size:13px; color:#374151; }
		.badge { background:#eef2ff; color:#3730a3; padding:2px 8px; border-radius:999px; font-size:12px; }
		.error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-top:12px; }
		.debug { background:#f8fafc; border:1px dashed #e5e7eb; padding:10px; border-radius:8px; margin-top:10px; color:#475569; font-size:12px; }
	</style>
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
		<main class="builder">
			<div class="page">
				<h2 style="margin:0 0 12px;">Survey Responses</h2>
				<div id="questionList"></div>
				<div id="errorBox" class="error" style="display:none;"></div>
				<div id="debugBox" class="debug"></div>
			</div>
		</main>
	</div>

	<script>
	// Derive surveyId from query (?survey=ID) or path /responses/ID
	(function(){
		const params = new URLSearchParams(location.search);
		let sid = params.get('survey');
		if(!sid){
			const m = location.pathname.match(/\/responses\/(\d+)/);
			if(m) sid = m[1];
		}
		window.__SURVEY_ID__ = sid ? parseInt(sid) : null;
	})();
	const surveyId = window.__SURVEY_ID__;
	const dbg = document.getElementById('debugBox');
	dbg.textContent = `surveyId=${surveyId} path=${location.pathname}`;
	async function load(){
		try {
			if(!surveyId){
				throw new Error('No surveyId in URL');
			}
			const analyticsUrl = `/api/surveys/${surveyId}/analytics`;
			const responsesUrl = `/api/surveys/${surveyId}/responses`;
			dbg.textContent += `\nGET ${analyticsUrl}\nGET ${responsesUrl}`;
			const [analyticsRes, responsesRes] = await Promise.all([
				fetch(analyticsUrl),
				fetch(responsesUrl)
			]);
			dbg.textContent += `\nStatus: ${analyticsRes.status}, ${responsesRes.status}`;
			if(!analyticsRes.ok || !responsesRes.ok){ throw new Error('Failed to load data'); }
			const analyticsJson = await analyticsRes.json();
			const respJson = await responsesRes.json();
			const responses = (respJson.data && respJson.data.data) ? respJson.data.data : (respJson.data || []);
			const first = responses[0] || {};
			const byQ = {};
			(first.answers || []).forEach(a=>{ byQ[a.question_id] = a; });
			const list = document.getElementById('questionList');
			list.innerHTML='';
			const arr = analyticsJson.data || [];
			if(arr.length === 0){ list.innerHTML = '<div class="stat">No questions to analyze.</div>'; return; }
			arr.forEach(q=>{
				const wrap = document.createElement('div'); wrap.className='q';
				const title = document.createElement('div'); title.className='q-title'; title.textContent = q.title;
				const grid = document.createElement('div'); grid.className='q-grid';
				const left = document.createElement('div');
				const right = document.createElement('div');
				renderAnalytics(right, q);
				const ans = byQ[q.question_id];
				left.innerHTML = ans ? renderAnswer(ans) : '<span class="badge">No individual response selected</span>';
				grid.appendChild(left); grid.appendChild(right);
				wrap.appendChild(title); wrap.appendChild(grid);
				list.appendChild(wrap);
			});
		} catch (e) {
			document.getElementById('errorBox').style.display='block';
			document.getElementById('errorBox').textContent = 'Failed to load analytics: ' + e.message;
		}
	}

	function renderAnswer(a){
		if(a.value){ return `<div class=\"stat\"><strong>Answer:</strong> ${a.value}</div>`; }
		if(a.option){ return `<div class=\"stat\"><strong>Answer:</strong> ${a.option.text}</div>`; }
		return `<div class=\"stat\">No answer</div>`;
	}
	function renderAnalytics(container, q){
		const hasChart = typeof window.Chart !== 'undefined';
		if(q.option_counts && q.option_counts.length){
			const box = document.createElement('div'); box.className='chart'; box.style.display='block'; container.appendChild(box);
			if(hasChart){
				const canvas = document.createElement('canvas'); canvas.height = 140; box.appendChild(canvas);
				const labels = q.option_counts.map(x=>x.label);
				const counts = q.option_counts.map(x=>x.count);
				new Chart(canvas, { type: 'bar', data: { labels, datasets: [{ label: 'Responses', data: counts, backgroundColor: '#60a5fa' }] }, options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, ticks:{precision:0}} } });
			} else {
				box.textContent = q.option_counts.map(x=>`${x.label}: ${x.count}`).join(' | ');
			}
			return;
		}
		if(q.average !== null){
			const box = document.createElement('div'); box.className='chart'; box.style.display='block'; container.appendChild(box);
			if(hasChart){
				const canvas = document.createElement('canvas'); canvas.height = 140; box.appendChild(canvas);
				const max = q.type === 'rating' ? 5 : Math.max(5, Math.ceil((q.average||0) * 1.2));
				new Chart(canvas, { type: 'doughnut', data: { labels: ['Avg','Remaining'], datasets: [{ data: [q.average, Math.max(0, max - q.average)], backgroundColor: ['#34d399','#e5e7eb'], borderWidth: 0 }] }, options: { plugins:{legend:{display:false}}, cutout:'70%' } });
				const caption = document.createElement('div'); caption.className='stat'; caption.style.textAlign='center'; caption.style.marginTop='8px'; caption.innerHTML = `Average: <strong>${q.average}</strong> (${q.total_answers} answers)`; box.appendChild(caption);
			} else {
				box.textContent = `Average: ${q.average} (${q.total_answers} answers)`;
			}
			return;
		}
		if(q.top_values && q.top_values.length){
			const box = document.createElement('div'); box.className='chart'; box.style.display='block'; container.appendChild(box);
			if(hasChart){
				const canvas = document.createElement('canvas'); canvas.height = 140; box.appendChild(canvas);
				new Chart(canvas, { type: 'bar', data: { labels: q.top_values.map(x=>x.value), datasets: [{ data: q.top_values.map(x=>x.count), backgroundColor:'#a78bfa' }] }, options:{ plugins:{legend:{display:false}}, indexAxis:'y', scales:{x:{beginAtZero:true, ticks:{precision:0}} }} });
			} else {
				box.textContent = q.top_values.map(x=>`${x.value}: ${x.count}`).join(' | ');
			}
			return;
		}
		const box = document.createElement('div'); box.className='chart'; box.textContent = `${q.total_answers} answers`; container.appendChild(box);
	}
	load();
	</script>
</body>
</html>


