<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fill Survey</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<style>
		.container { max-width: 900px; margin: 20px auto; padding: 0 12px; }
		.header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; }
		.footer { margin-top: 16px; display:flex; justify-content:flex-end; }
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<h2 id="title"></h2>
			<a class="btn" href="/">Builder</a>
		</div>
		<p id="desc"></p>
		<form id="form">
			<div class="q-card">
				<div class="q-header">Sign in to continue</div>
				<div class="q-body">
					<input type="email" id="respEmail" placeholder="your.name@gmail.com" required>
					<input type="text" id="respName" placeholder="Your name (optional)" style="margin-top:8px;">
					<small style="color:#6b7280;">Only Google emails are accepted (ends with @gmail.com).</small>
				</div>
			</div>
		</form>
		<div class="footer">
			<button class="btn primary" id="submitBtn">Submit</button>
		</div>
	</div>

	<script>
	const surveyId = {{ $surveyId }};
	let survey=null;
	async function loadSurvey(){
		const r = await fetch(`/api/surveys/${surveyId}`); survey = await r.json();
		document.getElementById('title').textContent = survey.title;
		document.getElementById('desc').textContent = survey.description || '';
		renderForm(survey);
	}
	
	function renderForm(s){
		const form = document.getElementById('form');
		// keep existing email card, then append dynamic questions
		s.questions.forEach(q=>{
			const card = document.createElement('div'); card.className='q-card';
			const h = document.createElement('div'); h.className='q-header'; 
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
			const b = document.createElement('div'); b.className='q-body';
			const name = `q_${q.id}`;
			switch(q.type){
				case 'short': { const i=document.createElement('input'); i.type='text'; i.name=name; b.appendChild(i); break; }
				case 'long': { const t=document.createElement('textarea'); t.name=name; b.appendChild(t); break; }
				case 'radio': { 
					q.options.forEach(o=>{ 
						const r=document.createElement('div'); 
						const i=document.createElement('input'); 
						i.type='radio'; 
						i.name=name; 
						i.value=o.id; 
						const l=document.createElement('label'); 
						l.textContent=o.label; 
						r.appendChild(i); 
						r.appendChild(l); 
						// Add green background for correct answers in quiz mode
						if (survey.type === 'quiz' && o.is_correct) {
							r.style.backgroundColor = '#dcfce7';
							r.style.border = '2px solid #16a34a';
							r.style.borderRadius = '8px';
							r.style.padding = '8px';
							r.style.margin = '4px 0';
						}
						b.appendChild(r); 
					}); 
					break; 
				}
				case 'checkbox': { 
					q.options.forEach(o=>{ 
						const r=document.createElement('div'); 
						const i=document.createElement('input'); 
						i.type='checkbox'; 
						i.name=name+'[]'; 
						i.value=o.id; 
						const l=document.createElement('label'); 
						l.textContent=o.label; 
						r.appendChild(i); 
						r.appendChild(l); 
						// Add green background for correct answers in quiz mode
						if (survey.type === 'quiz' && o.is_correct) {
							r.style.backgroundColor = '#dcfce7';
							r.style.border = '2px solid #16a34a';
							r.style.borderRadius = '8px';
							r.style.padding = '8px';
							r.style.margin = '4px 0';
						}
						b.appendChild(r); 
					}); 
					break; 
				}
				case 'dropdown': { 
					const s1=document.createElement('select'); 
					s1.name=name; 
					q.options.forEach(o=>{ 
						const opt=document.createElement('option'); 
						opt.value=o.id; 
						opt.textContent=o.label; 
						// Add green background for correct answers in quiz mode
						if (survey.type === 'quiz' && o.is_correct) {
							opt.style.backgroundColor = '#dcfce7';
							opt.style.color = '#166534';
						}
						s1.appendChild(opt); 
					}); 
					b.appendChild(s1); 
					break; 
				}
				case 'rating': { const i=document.createElement('input'); i.type='number'; i.min='1'; i.max='5'; i.name=name; b.appendChild(i); break; }
				case 'date': { const i=document.createElement('input'); i.type='date'; i.name=name; b.appendChild(i); break; }
				case 'number': { const i=document.createElement('input'); i.type='number'; i.name=name; b.appendChild(i); break; }
			}
			card.appendChild(b); form.appendChild(card);
		});
	}

	document.getElementById('submitBtn').addEventListener('click', async (e)=>{
		e.preventDefault();
		const answers=[]; const formEl=document.getElementById('form'); const form=new FormData(formEl);
		const email = document.getElementById('respEmail').value.trim();
		const name = document.getElementById('respName').value.trim();
		if (!email || !email.endsWith('@gmail.com')){ alert('Please use a valid Google email (gmail.com).'); return; }
		survey.questions.forEach(q=>{
			const nameKey = `q_${q.id}`;
			if(['radio','dropdown'].includes(q.type)){
				const v=form.get(nameKey); if(v) answers.push({question_id:q.id, option_id:parseInt(v)});
			}else if(q.type==='checkbox'){
				const arr=form.getAll(nameKey+'[]'); arr.forEach(v=>answers.push({question_id:q.id, option_id:parseInt(v)}));
			}else if(q.type==='rating' || q.type==='number' || q.type==='short' || q.type==='long' || q.type==='date'){
				const v=form.get(nameKey); if(v!==null && v!=='') answers.push({question_id:q.id, value:v});
			}
		});
		const payload = { respondent: { email, name }, answers };
		const res = await fetch(`/api/surveys/${surveyId}/responses`, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)});
		if(res.ok){ 
			const responseData = await res.json();
			// Redirect to results page with survey and response IDs
			window.location.href = `/results?survey=${surveyId}&response=${responseData.id}`;
		}
		else { const t = await res.text(); alert('Failed to submit: '+t); }
	});

	loadSurvey();
	</script>
</body>
</html>


