<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Survey Preview</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<script>
	// Toast Notification System - Initialize first
	class ToastManager {
		constructor() {
			this.container = null;
			this.init();
		}

		init() {
			this.container = document.createElement('div');
			this.container.id = 'toast-container';
			this.container.style.cssText = `
				position: fixed;
				top: 20px;
				right: 20px;
				z-index: 10000;
				display: flex;
				flex-direction: column;
				gap: 10px;
				max-width: 400px;
			`;
			document.body.appendChild(this.container);
		}

		show(message, type = 'info', duration = 4000) {
			const toast = document.createElement('div');
			toast.className = `toast toast-${type}`;
			
			const baseStyles = `
				padding: 16px 20px;
				border-radius: 12px;
				color: white;
				font-weight: 500;
				box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
				backdrop-filter: blur(10px);
				transform: translateX(100%);
				transition: all 0.3s ease;
				cursor: pointer;
				position: relative;
				overflow: hidden;
				min-width: 300px;
			`;

			let typeStyles = '';
			switch (type) {
				case 'success':
					typeStyles = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);';
					break;
				case 'error':
					typeStyles = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);';
					break;
				case 'warning':
					typeStyles = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);';
					break;
				case 'info':
				default:
					typeStyles = 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);';
					break;
			}

			toast.style.cssText = baseStyles + typeStyles;
			
			let icon = '';
			switch (type) {
				case 'success': icon = '✅'; break;
				case 'error': icon = '❌'; break;
				case 'warning': icon = '⚠️'; break;
				case 'info': icon = 'ℹ️'; break;
			}

			toast.innerHTML = `
				<div style="display: flex; align-items: center; gap: 10px;">
					<span style="font-size: 18px;">${icon}</span>
					<span>${message}</span>
					<span style="margin-left: auto; font-size: 20px; cursor: pointer; opacity: 0.7;" onclick="this.parentElement.parentElement.remove()">×</span>
				</div>
			`;

			this.container.appendChild(toast);

			setTimeout(() => {
				toast.style.transform = 'translateX(0)';
			}, 10);

			setTimeout(() => {
				this.remove(toast);
			}, duration);

			toast.addEventListener('click', () => {
				this.remove(toast);
			});

			return toast;
		}

		remove(toast) {
			if (toast && toast.parentElement) {
				toast.style.transform = 'translateX(100%)';
				toast.style.opacity = '0';
				setTimeout(() => {
					if (toast.parentElement) {
						toast.parentElement.removeChild(toast);
					}
				}, 300);
			}
		}

		success(message, duration = 4000) {
			return this.show(message, 'success', duration);
		}

		error(message, duration = 5000) {
			return this.show(message, 'error', duration);
		}

		warning(message, duration = 4500) {
			return this.show(message, 'warning', duration);
		}

		info(message, duration = 4000) {
			return this.show(message, 'info', duration);
		}
	}

	// Confirmation Dialog System
	class ConfirmDialog {
		static show(message, title = 'Confirm Action', options = {}) {
			return new Promise((resolve) => {
				const overlay = document.createElement('div');
				overlay.style.cssText = `
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background: rgba(0, 0, 0, 0.5);
					backdrop-filter: blur(5px);
					z-index: 10001;
					display: flex;
					align-items: center;
					justify-content: center;
					opacity: 0;
					transition: opacity 0.3s ease;
				`;

				const dialog = document.createElement('div');
				dialog.style.cssText = `
					background: white;
					border-radius: 16px;
					padding: 30px;
					max-width: 450px;
					width: 90%;
					box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
					transform: scale(0.9);
					transition: transform 0.3s ease;
				`;

				const confirmText = options.confirmText || 'Confirm';
				const cancelText = options.cancelText || 'Cancel';
				const type = options.type || 'warning';

				let icon = '';
				let iconColor = '';
				switch (type) {
					case 'danger':
						icon = '🗑️';
						iconColor = '#ef4444';
						break;
					case 'warning':
						icon = '⚠️';
						iconColor = '#f59e0b';
						break;
					case 'info':
						icon = 'ℹ️';
						iconColor = '#3b82f6';
						break;
					case 'publish':
						icon = '🚀';
						iconColor = '#10b981';
						break;
				}

				dialog.innerHTML = `
					<div style="text-align: center; margin-bottom: 25px;">
						<div style="font-size: 48px; margin-bottom: 15px;">${icon}</div>
						<h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 20px;">${title}</h3>
						<p style="margin: 0; color: #6b7280; line-height: 1.5;">${message}</p>
					</div>
					<div style="display: flex; gap: 12px; justify-content: center;">
						<button id="cancelBtn" style="
							padding: 12px 24px;
							border: 2px solid #e5e7eb;
							background: white;
							color: #374151;
							border-radius: 8px;
							font-weight: 600;
							cursor: pointer;
							transition: all 0.2s ease;
							min-width: 100px;
						">${cancelText}</button>
						<button id="confirmBtn" style="
							padding: 12px 24px;
							border: none;
							background: ${iconColor};
							color: white;
							border-radius: 8px;
							font-weight: 600;
							cursor: pointer;
							transition: all 0.2s ease;
							min-width: 100px;
						">${confirmText}</button>
					</div>
				`;

				overlay.appendChild(dialog);
				document.body.appendChild(overlay);

				setTimeout(() => {
					overlay.style.opacity = '1';
					dialog.style.transform = 'scale(1)';
				}, 10);

				const confirmBtn = dialog.querySelector('#confirmBtn');
				const cancelBtn = dialog.querySelector('#cancelBtn');

				const cleanup = () => {
					overlay.style.opacity = '0';
					dialog.style.transform = 'scale(0.9)';
					setTimeout(() => {
						if (overlay.parentElement) {
							overlay.parentElement.removeChild(overlay);
						}
					}, 300);
				};

				confirmBtn.addEventListener('click', () => {
					cleanup();
					resolve(true);
				});

				cancelBtn.addEventListener('click', () => {
					cleanup();
					resolve(false);
				});

				overlay.addEventListener('click', (e) => {
					if (e.target === overlay) {
						cleanup();
						resolve(false);
					}
				});
			});
		}
	}

	// Initialize global instances immediately
	window.toast = new ToastManager();
	window.confirmDialog = ConfirmDialog;
	
	console.log('Toast system initialized in preview');
	</script>
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
			<div style="display: flex; align-items: center; gap: 15px;">
				<button onclick="history.back()" class="btn" style="background: #6b7280; color: white;">← Back</button>
				<h1>Survey Preview</h1>
			</div>
			<div class="share" style="display: flex; gap: 12px; align-items: center;">
				<input class="input" id="shareUrl" readonly placeholder="Survey link will appear here after publishing..." style="min-width: 350px;">
				<button class="btn primary" id="publishBtn" onclick="publishSurvey()" style="background: #3b82f6; color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
					🚀 Publish
				</button>
				<button class="btn" id="copyBtn" onclick="copyToClipboard()" style="background: #10b981; color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: none;" title="Copy Link">
					📋 Copy
				</button>
			</div>
		</div>
		<div id="previewRoot"></div>
	</div>

	<script>
	const surveyId = {{ $surveyId ?? 'null' }};
	async function loadSurvey() {
		if (!surveyId || surveyId === null) {
			document.getElementById('previewRoot').innerHTML = '<div style="text-align: center; padding: 40px; color: #666;"><h3>No survey to preview</h3><p>Please save your survey first, then click Preview.</p></div>';
			return;
		}
		
		try {
			const res = await fetch(`/api/surveys/${surveyId}`);
			if (!res.ok) {
				throw new Error('Survey not found');
			}
			const survey = await res.json();
			document.title = `Preview - ${survey.title}`;
			renderSurvey(survey);
			
			// If survey is already published, show the link
			if (survey.is_published) {
				const url = `${location.origin}/s/${surveyId}`;
				document.getElementById('shareUrl').value = url;
				document.getElementById('shareUrl').style.display = 'block';
				document.getElementById('copyBtn').style.display = 'inline-block';
			}
		} catch (error) {
			console.error('Error loading survey:', error);
			document.getElementById('previewRoot').innerHTML = '<div style="text-align: center; padding: 40px; color: #666;"><h3>Error loading survey</h3><p>The survey could not be loaded. Please try again.</p></div>';
		}
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
		const confirmed = await confirmDialog.show(
			'Do you want to publish this survey? It will become available to the public after publishing.',
			'Confirm Publish',
			{
				confirmText: 'Publish',
				cancelText: 'Cancel',
				type: 'publish'
			}
		);
		
		if (!confirmed) return;
		
		try {
			window.toast.info('Publishing survey...');
			const pub = await fetch(`/api/surveys/${surveyId}/publish`, { method: 'POST' });
			if(pub.ok){
				const url = `${location.origin}/s/${surveyId}`;
				document.getElementById('shareUrl').value = url;
				document.getElementById('shareUrl').style.display = 'block';
				document.getElementById('copyBtn').style.display = 'inline-block';
				window.toast.success('Survey published successfully!');
			} else {
				window.toast.error('Failed to publish survey');
			}
		} catch (error) {
			window.toast.error('Error occurred while publishing survey');
		}
	});

	// Copy share URL function
	function copyShareUrl() {
		const shareUrl = document.getElementById('shareUrl');
		if (shareUrl.value) {
			navigator.clipboard.writeText(shareUrl.value).then(() => {
				window.toast.success('Link copied to clipboard!');
			}).catch(() => {
				// Fallback for older browsers
				shareUrl.select();
				document.execCommand('copy');
				window.toast.success('Link copied to clipboard!');
			});
		}
	}

	// Publish Survey Function
	async function publishSurvey() {
		console.log('Publishing survey with ID:', surveyId);
		
		if (!surveyId || surveyId === null || surveyId === 'null') {
			if (typeof window.toast !== 'undefined') {
				window.toast.error('No survey to publish. Please save your survey first.');
			}
			return;
		}

		// Disable button during publishing
		const publishBtn = document.getElementById('publishBtn');
		const originalText = publishBtn.innerHTML;
		publishBtn.disabled = true;
		publishBtn.innerHTML = '⏳ Publishing...';

		try {
			if (typeof window.toast !== 'undefined') {
				window.toast.info('Publishing survey...', 3000);
			}
			
			const response = await fetch(`/api/surveys/${surveyId}/publish`, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
				}
			});

			console.log('Publish response status:', response.status);

			if (!response.ok) {
				const errorData = await response.json().catch(() => ({}));
				console.error('Publish error data:', errorData);
				throw new Error(errorData.message || errorData.error || `HTTP ${response.status}`);
			}

			const data = await response.json();
			console.log('Publish success data:', data);
			
			// Generate the public survey link
			const publicLink = `${window.location.origin}/s/${surveyId}`;
			
			// Update the input field
			const shareUrl = document.getElementById('shareUrl');
			shareUrl.value = publicLink;
			
			// Show copy button
			const copyBtn = document.getElementById('copyBtn');
			copyBtn.style.display = 'inline-block';
			
			// Update button text
			publishBtn.innerHTML = '✅ Published';
			publishBtn.style.background = '#10b981';
			
			if (typeof window.toast !== 'undefined') {
				window.toast.success('🎉 Survey published successfully! Link is ready to share.');
			}
			
		} catch (error) {
			console.error('Publish error:', error);
			if (typeof window.toast !== 'undefined') {
				window.toast.error(`Failed to publish survey: ${error.message}`);
			}
			
			// Reset button
			publishBtn.innerHTML = originalText;
			publishBtn.disabled = false;
		}
	}

	// Copy to Clipboard Function
	async function copyToClipboard() {
		const shareUrl = document.getElementById('shareUrl');
		const link = shareUrl.value;
		
		if (!link) {
			if (typeof window.toast !== 'undefined') {
				window.toast.warning('No link to copy. Please publish the survey first.');
			}
			return;
		}

		// Visual feedback
		const copyBtn = document.getElementById('copyBtn');
		const originalText = copyBtn.innerHTML;
		copyBtn.innerHTML = '⏳ Copying...';
		copyBtn.disabled = true;

		try {
			// Modern clipboard API
			if (navigator.clipboard && window.isSecureContext) {
				await navigator.clipboard.writeText(link);
			} else {
				// Fallback for older browsers
				shareUrl.select();
				shareUrl.setSelectionRange(0, 99999); // For mobile devices
				document.execCommand('copy');
			}
			
			// Success feedback
			copyBtn.innerHTML = '✅ Copied!';
			copyBtn.style.background = '#059669';
			if (typeof window.toast !== 'undefined') {
				window.toast.success('📋 Link copied to clipboard!');
			}
			
			// Reset button after 2 seconds
			setTimeout(() => {
				copyBtn.innerHTML = originalText;
				copyBtn.style.background = '#10b981';
				copyBtn.disabled = false;
			}, 2000);
			
		} catch (error) {
			console.error('Copy failed:', error);
			if (typeof window.toast !== 'undefined') {
				window.toast.error('Failed to copy link. Please copy manually.');
			}
			
			// Reset button
			copyBtn.innerHTML = originalText;
			copyBtn.disabled = false;
		}
	}

	loadSurvey();
	</script>
</body>
</html>


