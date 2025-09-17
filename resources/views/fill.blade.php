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
		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			margin: 0;
			padding: 20px;
		}
		.container { 
			max-width: 900px; 
			margin: 20px auto; 
			padding: 30px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
		}
		.header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; }
		.footer { margin-top: 16px; display:flex; justify-content:flex-end; }
		h2 {
			color: #4a5568;
			font-size: 28px;
			margin-bottom: 8px;
		}
		#desc {
			color: #718096;
			margin-bottom: 24px;
		}
		.q-card {
			background: rgba(255, 255, 255, 0.9);
			backdrop-filter: blur(5px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
		}
		.btn.primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			color: white;
			font-weight: 600;
			transition: all 0.3s ease;
		}
		.btn.primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
		}
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
		// render dynamic questions with spacing
		s.questions.forEach((q, index)=>{
			const card = document.createElement('div'); 
			card.className='q-card';
			// Add margin bottom for spacing between questions
			card.style.marginBottom = '24px';
			
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
				case 'short': { 
					const i=document.createElement('input'); 
					i.type='text'; 
					i.name=name; 
					i.placeholder='Enter your answer...';
					b.appendChild(i); 
					break; 
				}
				case 'long': { 
					const t=document.createElement('textarea'); 
					t.name=name; 
					t.placeholder='Enter your detailed answer...';
					t.rows=4;
					b.appendChild(t); 
					break; 
				}
				case 'radio': { 
					q.options.forEach((o, idx)=>{ 
						const r=document.createElement('div'); 
						r.style.display='flex';
						r.style.alignItems='center';
						r.style.marginBottom='8px';
						const i=document.createElement('input'); 
						i.type='radio'; 
						i.name=name; 
						// Use option index if no ID available
						i.value=o.id || idx; 
						i.id=`${name}_${o.id || idx}`;
						i.style.marginRight='8px';
						const l=document.createElement('label'); 
						l.textContent=o.label || o; 
						l.htmlFor=`${name}_${o.id || idx}`;
						l.style.cursor='pointer';
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
					q.options.forEach((o, idx)=>{ 
						const r=document.createElement('div'); 
						r.style.display='flex';
						r.style.alignItems='center';
						r.style.marginBottom='8px';
						const i=document.createElement('input'); 
						i.type='checkbox'; 
						i.name=name+'[]'; 
						// Use option index if no ID available
						i.value=o.id || idx; 
						i.id=`${name}_${o.id || idx}`;
						i.style.marginRight='8px';
						const l=document.createElement('label'); 
						l.textContent=o.label || o; 
						l.htmlFor=`${name}_${o.id || idx}`;
						l.style.cursor='pointer';
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
					s1.style.width='100%';
					s1.style.padding='8px';
					s1.style.border='1px solid #e5e7eb';
					s1.style.borderRadius='8px';
					// Add default option
					const defaultOpt = document.createElement('option');
					defaultOpt.value = '';
					defaultOpt.textContent = 'Select an option...';
					s1.appendChild(defaultOpt);
					q.options.forEach((o, idx)=>{ 
						const opt=document.createElement('option'); 
						// Use option index if no ID available
						opt.value=o.id || idx; 
						opt.textContent=o.label || o; 
						if (survey.type === 'quiz' && o.is_correct) {
							opt.style.backgroundColor = '#dcfce7';
							opt.style.color = '#166534';
						}
						s1.appendChild(opt); 
					}); 
					b.appendChild(s1); 
					break; 
				}
				case 'rating': { 
					const ratingDiv = document.createElement('div');
					ratingDiv.style.display = 'flex';
					ratingDiv.style.alignItems = 'center';
					ratingDiv.style.gap = '8px';
					
					for(let i = 1; i <= 5; i++) {
						const star = document.createElement('span');
						star.textContent = '★';
						star.style.fontSize = '24px';
						star.style.color = '#d1d5db';
						star.style.cursor = 'pointer';
						star.style.transition = 'color 0.2s';
						star.dataset.rating = i;
						
						star.addEventListener('click', () => {
							const hiddenInput = ratingDiv.querySelector('input[type="hidden"]') || document.createElement('input');
							hiddenInput.type = 'hidden';
							hiddenInput.name = name;
							hiddenInput.value = i;
							if (!ratingDiv.querySelector('input[type="hidden"]')) {
								ratingDiv.appendChild(hiddenInput);
							}
							
							// Update star colors
							ratingDiv.querySelectorAll('span').forEach((s, idx) => {
								s.style.color = idx < i ? '#f59e0b' : '#d1d5db';
							});
						});
						
						ratingDiv.appendChild(star);
					}
					
					b.appendChild(ratingDiv); 
					break; 
				}
				case 'date': { 
					const i=document.createElement('input'); 
					i.type='date'; 
					i.name=name; 
					i.style.width='100%';
					i.style.padding='8px';
					i.style.borderRadius='4px';
					i.style.border='1px solid #d1d5db';
					b.appendChild(i); 
					break; 
				}
				case 'number': { 
					const i=document.createElement('input'); 
					i.type='number'; 
					i.name=name; 
					i.placeholder='Enter a number...';
					i.style.width='100%';
					i.style.padding='8px';
					i.style.borderRadius='4px';
					i.style.border='1px solid #d1d5db';
					b.appendChild(i); 
					break; 
				}
			}
			card.appendChild(b); form.appendChild(card);
		});
	}

	document.getElementById('submitBtn').addEventListener('click', async (e)=>{
		e.preventDefault();
		
		try {
			console.log('Submit button clicked');
			
			const answers=[]; 
			const formEl=document.getElementById('form'); 
			const form=new FormData(formEl);
			
			console.log('Survey object:', survey);
			console.log('Form element:', formEl);
			
			// Collect answers from form without requiring email
			survey.questions.forEach(q=>{
				const nameKey = `q_${q.id}`;
				console.log(`Processing question ${q.id} (${q.type}):`, nameKey);
				
				if(['radio','dropdown'].includes(q.type)){
					const v=form.get(nameKey); 
					console.log(`Radio/dropdown value for ${nameKey}:`, v);
					if(v && v !== '') {
						const optionId = isNaN(parseInt(v)) ? v : parseInt(v);
						answers.push({question_id:q.id, option_id:optionId});
					}
				}else if(q.type==='checkbox'){
					const arr=form.getAll(nameKey+'[]'); 
					console.log(`Checkbox values for ${nameKey}[]:`, arr);
					arr.forEach(v=>{
						if(v && v !== '') {
							const optionId = isNaN(parseInt(v)) ? v : parseInt(v);
							answers.push({question_id:q.id, option_id:optionId});
						}
					});
				}else if(q.type==='rating' || q.type==='number' || q.type==='short' || q.type==='long' || q.type==='date'){
					const v=form.get(nameKey); 
					console.log(`Text/number value for ${nameKey}:`, v);
					if(v!==null && v!=='') answers.push({question_id:q.id, value:String(v)});
				}
			});
			
			console.log('Collected answers:', answers);
			
			// Validate payload before sending
			const payload = { 
				respondent: { 
					email: 'anonymous@survey.com', 
					name: 'Anonymous User' 
				}, 
				answers 
			};
			
			// Clean payload to ensure valid JSON
			const cleanPayload = JSON.parse(JSON.stringify(payload));
			console.log('Payload to send:', cleanPayload);
			
			const res = await fetch(`/api/surveys/${surveyId}/responses`, {
				method:'POST', 
				headers:{
					'Content-Type':'application/json',
					'Accept': 'application/json'
				}, 
				body:JSON.stringify(cleanPayload)
			});
			
			console.log('Response status:', res.status);
			
			if(res.ok){ 
				const responseData = await res.json();
				console.log('Response data:', responseData);
				alert('Survey submitted successfully!');
				// Redirect to results page with survey and response IDs
				window.location.href = `/results?survey=${surveyId}&response=${responseData.id}`;
			}
			else { 
				const errorText = await res.text(); 
				console.error('Submit error:', errorText);
				alert('Failed to submit: ' + errorText); 
			}
		} catch (error) {
			console.error('Submit error:', error);
			alert('An error occurred while submitting: ' + error.message);
		}
	});

	loadSurvey();
	</script>
</body>
</html>


