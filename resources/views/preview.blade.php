<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Survey Preview</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<style>
		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			margin: 0;
			padding: 20px;
		}
		.builder {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
			padding: 30px;
		}
		.header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; }
		.share { display:flex; gap:8px; align-items:center; }
		.input { padding:8px 10px; border:1px solid #e5e7eb; border-radius:8px; min-width: 280px; }
		#previewRoot h3 {
			color: #4a5568;
			font-size: 28px;
			margin-bottom: 8px;
		}
		#previewRoot p {
			color: #718096;
			margin-bottom: 24px;
		}
		.q-card {
			background: rgba(255, 255, 255, 0.9);
			backdrop-filter: blur(5px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
		}
	</style>
</head>
<body>
	<div class="builder" style="max-width: 900px; margin: 20px auto;">
		<div class="header">
			<h2>Preview</h2>
			<div class="share">
				<input class="input" id="shareUrl" readonly>
				<button class="btn primary" id="publishBtn">Publish & Get Link</button>
			</div>
		</div>
		<div id="previewRoot"></div>
	</div>

	<script>
	const surveyId = {{ $surveyId }};
	async function loadSurvey() {
		const res = await fetch(`/api/surveys/${surveyId}`);
		const survey = await res.json();
		document.title = `Preview - ${survey.title}`;
		renderSurvey(survey);
	}

	function renderSurvey(s) {
		const root = document.getElementById('previewRoot');
		root.innerHTML = '';
		const title = document.createElement('h3'); title.textContent = s.title; root.appendChild(title);
		if (s.description) { const d = document.createElement('p'); d.textContent = s.description; root.appendChild(d); }
		s.questions.forEach(q => {
			const card = document.createElement('div'); 
			card.className = 'q-card';
			// Add margin bottom for spacing between questions
			card.style.marginBottom = '24px';
			
			const h = document.createElement('div'); h.className = 'q-header'; 
			h.innerHTML = q.title + (q.required ? ' <span style="color: red; margin-left: 4px;">*</span>' : ''); 
			card.appendChild(h);
			if (q.description) {
				const desc = document.createElement('div'); 
				desc.className = 'q-description'; 
				desc.style.fontSize = '14px'; 
				desc.style.color = '#6b7280'; 
				desc.style.marginTop = '4px';
				desc.textContent = q.description; 
				card.appendChild(desc);
			}
			const b = document.createElement('div'); b.className = 'q-body';
			switch(q.type){
				case 'short': { const i=document.createElement('input'); i.type='text'; b.appendChild(i); break; }
				case 'long': { const t=document.createElement('textarea'); b.appendChild(t); break; }
				case 'radio': { q.options.forEach(o=>{ const r=document.createElement('div'); const i=document.createElement('input'); i.type='radio'; i.name=`q_${q.id}`; i.value=o.id; const l=document.createElement('label'); l.textContent=o.label; r.appendChild(i); r.appendChild(l); b.appendChild(r); }); break; }
				case 'checkbox': { q.options.forEach(o=>{ const r=document.createElement('div'); const i=document.createElement('input'); i.type='checkbox'; i.name=`q_${q.id}[]`; i.value=o.id; const l=document.createElement('label'); l.textContent=o.label; r.appendChild(i); r.appendChild(l); b.appendChild(r); }); break; }
				case 'dropdown': { const s1=document.createElement('select'); q.options.forEach(o=>{ const opt=document.createElement('option'); opt.value=o.id; opt.textContent=o.label; s1.appendChild(opt); }); b.appendChild(s1); break; }
				case 'rating': { const d=document.createElement('div'); for(let i=1;i<=5;i++){ const s2=document.createElement('span'); s2.textContent='★'; s2.style.color='#f59e0b'; s2.style.fontSize='20px'; s2.style.marginInline='2px'; d.appendChild(s2);} b.appendChild(d); break; }
				case 'date': { const i=document.createElement('input'); i.type='date'; b.appendChild(i); break; }
				case 'number': { const i=document.createElement('input'); i.type='number'; b.appendChild(i); break; }
			}
			card.appendChild(b);
			root.appendChild(card);
		});
	}

	document.getElementById('publishBtn').addEventListener('click', async ()=>{
		const pub = await fetch(`/api/surveys/${surveyId}/publish`, { method: 'POST' });
		if(pub.ok){
			const url = `${location.origin}/s/${surveyId}`;
			document.getElementById('shareUrl').value = url;
			alert('Published. Share the link above.');
		}else{
			alert('Failed to publish');
		}
	});

	loadSurvey();
	</script>
</body>
</html>


