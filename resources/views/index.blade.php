<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Academic Platform - Survey Builder</title>
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
	<div class="app">
		<!-- Sidebar -->
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title"> AQL Soft</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item active" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item" href="{{ url('/surveys') }}">⚙️ <span>Manage Surveys</span></a>
				<!-- <a class="menu-item" href="#">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a> -->
			</nav>
		</aside>

		<!-- Field types list -->
		<section class="field-types">
			<!-- Survey Statistics -->
			<div class="survey-stats-sidebar">
				<h3 class="section-title">Survey Statistics</h3>
				<div class="stats-cards">
					<div class="stat-card">
						<div class="stat-icon">📝</div>
						<div class="stat-info">
							<div class="stat-value" id="totalQuestions">0</div>
							<div class="stat-label">Questions</div>
						</div>
					</div>
					<div class="stat-card">
						<div class="stat-icon">⚖️</div>
						<div class="stat-info">
							<div class="stat-value" id="totalWeight">0</div>
							<div class="stat-label">Max Weight</div>
						</div>
					</div>
				</div>
			</div>

			<h3 class="section-title">Field Types</h3>
			<div class="field-list" id="fieldList">
				<!-- Cards populated by JS to keep one source of truth -->
			</div>
		</section>

		<!-- Builder canvas -->
		<main class="builder">
			<div style="display:flex; justify-content: flex-end; margin-bottom: 10px; gap: 8px;">
				<form id="csvImportForm" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
					<input type="file" id="csvFile" name="file" accept=".csv" />
					<select id="importType">
						<option value="survey">Survey</option>
						<option value="quiz">Quiz</option>
					</select>
					<button type="submit" class="btn">📥 Import from CSV</button>
					<button type="button" class="btn" onclick="showCSVHelp()" style="background: #6b7280;">❓ CSV Format</button>
				</form>
			</div>
			<section class="survey-header">
				<div class="survey-type-selector">
					<label class="type-option">
						<input type="radio" name="surveyType" value="survey" checked>
						<span class="type-card">
							<div class="type-icon">📊</div>
							<div class="type-title">Survey</div>
							<div class="type-desc">Collect opinions and feedback</div>
						</span>
					</label>
					<label class="type-option">
						<input type="radio" name="surveyType" value="quiz">
						<span class="type-card">
							<div class="type-icon">🎯</div>
							<div class="type-title">Quiz</div>
							<div class="type-desc">Test knowledge with correct answers</div>
						</span>
					</label>
				</div>
				<input class="survey-title" id="surveyTitle" value="New Survey" />
				<input class="survey-desc" id="surveyDesc" value="Survey description" />
			</section>

			<section class="dropzone" id="dropzone" aria-label="Questions dropzone">
				<div class="empty-state" id="emptyState">
					<div class="empty-title">Start Building Your Survey</div>
					<div class="empty-sub">Drag fields from the left or click to add them</div>
					<button class="btn primary" id="addFirstBtn">+ Add your first question</button>
				</div>
			</section>

			<!-- Floating actions -->
			<div class="floating-actions">
				<button class="btn" id="previewBtn">👁️ Preview</button>
				<button class="btn primary" id="publishBtn">Publish Survey</button>
			</div>
		</main>
	</div>

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
	
	console.log('Toast system initialized');
	</script>
	<script src="{{ asset('assets/js/script.js') }}"></script>
	<script>
		// Handle survey type selection
		document.addEventListener('DOMContentLoaded', function() {
			const surveyTypeInputs = document.querySelectorAll('input[name="surveyType"]');
			
			// Survey type inputs are handled by the existing logic
			
			// Setup CSV import with delay to ensure script.js is loaded
			setTimeout(() => {
				setupCSVImport();
			}, 100);
		});
		
		function setupCSVImport() {
			const importForm = document.getElementById('csvImportForm');
			if (importForm) {
				importForm.addEventListener('submit', async (e) => {
					e.preventDefault();
					console.log('CSV Import form submitted');
					
					const fileInput = document.getElementById('csvFile');
					if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
						alert('Choose a CSV file');
						return;
					}
					
					const file = fileInput.files[0];
					console.log('File selected:', file.name);
					
					try {
						const text = await file.text();
						console.log('File content:', text.substring(0, 200));
						
						const type = document.getElementById('importType')?.value || 'survey';
						
						const lines = text.split('\n').filter(line => line.trim());
						if (lines.length === 0) throw new Error('Empty CSV file');
						
						// Check for duplicate questions - only check against existing questions, not within CSV
						const existingTitles = window.questions ? window.questions.map(q => q.title.toLowerCase().trim()) : [];
						const duplicates = [];
						
						// Only check for duplicates against existing questions
						lines.forEach(line => {
							const parts = line.split(',').map(part => part.trim().replace(/^"|"$/g, ''));
							if (parts.length >= 1) {
								const questionTitle = parts[0].toLowerCase().trim();
								if (existingTitles.includes(questionTitle)) {
									duplicates.push(parts[0]);
								}
							}
						});
						
						if (duplicates.length > 0) {
							const proceed = confirm(`Found ${duplicates.length} questions that already exist:\n${duplicates.join('\n')}\n\nDo you want to import the remaining questions and skip duplicates?`);
							if (!proceed) return;
						}
						
						const imported = [];
						
						lines.forEach((line, index) => {
							const parts = line.split(',').map(part => part.trim().replace(/^"|"$/g, ''));
							if (parts.length < 2) return;
							
							const questionTitle = parts[0];
							
							// Skip if this question already exists
							if (existingTitles.includes(questionTitle.toLowerCase().trim())) {
								return;
							}
							
							const questionType = parts[1] || 'short';
							const options = parts.slice(2).filter(opt => opt.length > 0);
							
							const id = `q_${Date.now()}_${Math.random().toString(36).slice(2, 6)}_${index}`;
							const mapped = {
								id,
								type: ['short', 'long', 'radio', 'checkbox', 'dropdown', 'rating', 'date', 'number'].includes(questionType) ? questionType : 'short',
								title: questionTitle || `Question ${index + 1}`,
								description: '',
								options: (questionType === 'radio' || questionType === 'checkbox' || questionType === 'dropdown') ? options : [],
								correctAnswer: null,
								weights: {},
								points: {},
								questionPoints: 1,
								questionWeight: 1.0,
								required: false
							};
							
							if (mapped.options.length > 0) {
								mapped.options.forEach((_, idx) => {
									mapped.weights[idx] = 1;
									mapped.points[idx] = 1;
								});
							}
							
							// Ensure all required fields are present for validation
							if (!mapped.weights) mapped.weights = {};
							if (!mapped.points) mapped.points = {};
							if (typeof mapped.required === 'undefined') mapped.required = false;
							if (typeof mapped.questionPoints === 'undefined') mapped.questionPoints = 1;
							if (typeof mapped.questionWeight === 'undefined') mapped.questionWeight = 1.0;
							
							imported.push(mapped);
						});
						
						console.log('Imported questions:', imported);
				
				// Ensure questions array exists in global scope
				if (typeof window.questions === 'undefined') {
					window.questions = [];
				}
				
				imported.forEach(q => window.questions.push(q));
				
				// Set currentSurveyType in global scope
				window.currentSurveyType = type;
				if (typeof currentSurveyType !== 'undefined') {
					currentSurveyType = type;
				}
				
				const typeRadio = document.querySelector(`input[name="surveyType"][value="${type}"]`);
				if (typeRadio) typeRadio.checked = true;
				
				// Try to call functions if they exist
				try {
					// Use renderQuestions if available, otherwise manual render
					if (typeof window.renderQuestions === 'function') {
						window.renderQuestions();
					} else if (typeof renderQuestions === 'function') {
						renderQuestions();
					} else {
						manuallyRenderQuestions();
					}
					
					const skipped = duplicates.length;
					const message = skipped > 0 ? 
						`Successfully imported ${imported.length} questions from CSV (${skipped} duplicates skipped). You can now drag questions to reorder them.` :
						`Successfully imported ${imported.length} questions from CSV. You can now drag questions to reorder them.`;
					
					alert(message);
					fileInput.value = '';
					
					try {
						if (typeof window.updateSurveyStats === 'function') {
							window.updateSurveyStats();
						} else if (typeof updateSurveyStats === 'function') {
							updateSurveyStats();
						} else {
							console.warn('updateSurveyStats function not found');
						}
					} catch (e) {
						console.error('Error calling updateSurveyStats:', e);
					}
				} catch (e) {
					console.error('Error calling renderQuestions:', e);
					// Fallback to manual render
					manuallyRenderQuestions();
				}
			} catch (err) {
				console.error('CSV Import error:', err);
				alert('CSV Import error: ' + err.message);
			}
		});
	} else {
		console.log('CSV Import form not found');
	}
}

// Manual render function as fallback
function manuallyRenderQuestions() {
	console.log('Manual render called, questions:', window.questions);
	const dropzone = document.getElementById('dropzone');
	if (!dropzone) {
		console.error('Dropzone not found');
		return;
	}
	
	const emptyState = document.getElementById('emptyState');
	if (emptyState && window.questions && window.questions.length > 0) {
		emptyState.style.display = 'none';
	}
	
	// Don't clear dropzone if it already has questions, just update container
	let container = dropzone.querySelector('.questions');
	if (!container) {
		container = document.createElement('div');
		container.className = 'questions';
		dropzone.appendChild(container);
	} else {
		container.innerHTML = '';
	}
	
	if (window.questions && window.questions.length > 0) {
		window.questions.forEach((q, index) => {
			const card = document.createElement('div');
			card.className = 'q-card';
			card.dataset.id = q.id;
			card.setAttribute('draggable', 'true');
			card.style.cssText = 'background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px;';
			
			// Add drag and drop events
			card.addEventListener('dragstart', (e) => {
				e.dataTransfer.setData('text/plain', q.id);
				card.style.opacity = '0.5';
			});
			
			card.addEventListener('dragend', (e) => {
				card.style.opacity = '1';
			});
			
			card.addEventListener('dragover', (e) => {
				e.preventDefault();
				card.style.borderColor = '#3B82F6';
			});
			
			card.addEventListener('dragleave', (e) => {
				card.style.borderColor = '#e5e7eb';
			});
			
			card.addEventListener('drop', (e) => {
				e.preventDefault();
				card.style.borderColor = '#e5e7eb';
				
				const draggedId = e.dataTransfer.getData('text/plain');
				const targetId = q.id;
				
				if (!draggedId || draggedId === targetId) return;
				
				// Check if this is a field type being dragged (not a question reorder)
				const fieldTypes = ['short', 'long', 'radio', 'checkbox', 'dropdown', 'rating', 'date', 'number'];
				if (fieldTypes.includes(draggedId)) {
					// This is a new field being added, don't process as reorder
					return;
				}
				
				const fromIndex = window.questions.findIndex(x => x.id === draggedId);
				const toIndex = window.questions.findIndex(x => x.id === targetId);
				
				if (fromIndex === -1 || toIndex === -1) return;
				
				// Reorder questions array
				const [movedQuestion] = window.questions.splice(fromIndex, 1);
				window.questions.splice(toIndex, 0, movedQuestion);
				
				// Re-render questions
				manuallyRenderQuestions();
			});
			
			const header = document.createElement('div');
			header.className = 'q-header';
			header.style.cssText = 'display: flex; align-items: flex-start; margin-bottom: 12px;';
			
			// Drag handle
			const handle = document.createElement('div');
			handle.className = 'q-handle';
			handle.textContent = '⋮⋮';
			handle.style.cssText = 'cursor: grab; margin-right: 8px; color: #9CA3AF; font-size: 14px;';
			handle.title = 'Drag to reorder';
			
			// Make handle the drag initiator
			handle.addEventListener('mousedown', () => {
				handle.style.cursor = 'grabbing';
			});
			
			handle.addEventListener('mouseup', () => {
				handle.style.cursor = 'grab';
			});
			
			// Title and description container
			const titleContainer = document.createElement('div');
			titleContainer.style.cssText = 'flex: 1;';
			
			const title = document.createElement('div');
			title.className = 'q-title-display';
			title.textContent = q.title;
			title.style.cssText = 'font-weight: 600; font-size: 16px; cursor: pointer;';
			title.addEventListener('click', () => {
				showTitleEditor(title, q);
			});
			
			const description = document.createElement('div');
			description.className = 'q-desc-display';
			description.textContent = q.description || 'Click to add description (optional)';
			description.style.cssText = `color: ${q.description ? '#374151' : '#9CA3AF'}; font-size: 14px; font-style: ${q.description ? 'normal' : 'italic'}; margin-top: 4px; cursor: pointer;`;
			description.addEventListener('click', () => {
				showDescriptionEditor(description, q);
			});
			
			titleContainer.appendChild(title);
			titleContainer.appendChild(description);
			
			const actions = document.createElement('div');
			actions.className = 'q-actions';
			actions.style.cssText = 'display: flex; gap: 8px;';
			
			// Settings button
			const settingsBtn = document.createElement('button');
			settingsBtn.innerHTML = '⚙️';
			settingsBtn.title = 'Settings';
			settingsBtn.style.cssText = 'background: none; border: none; cursor: pointer; font-size: 16px;';
			settingsBtn.addEventListener('click', () => {
				showQuestionSettings(q);
			});
			
			// Delete button
			const deleteBtn = document.createElement('button');
			deleteBtn.innerHTML = '🗑️';
			deleteBtn.title = 'Delete';
			deleteBtn.style.cssText = 'background: none; border: none; cursor: pointer; font-size: 16px;';
			deleteBtn.onclick = () => {
				card.remove();
				window.questions = window.questions.filter(quest => quest.id !== q.id);
			};
			
			actions.appendChild(settingsBtn);
			actions.appendChild(deleteBtn);
			
			header.appendChild(handle);
			header.appendChild(titleContainer);
			header.appendChild(actions);
			
			const body = document.createElement('div');
			body.className = 'q-body';
			body.style.cssText = 'margin-top: 12px;';
			
			// Question content based on type
			if (q.options && q.options.length > 0) {
				q.options.forEach((opt, optIndex) => {
					const optDiv = document.createElement('div');
					optDiv.className = 'option';
					optDiv.style.cssText = 'padding: 8px 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;';
					
					const optText = document.createElement('span');
					optText.textContent = opt;
					
					// Weight/Points for each option
					const surveyType = document.querySelector('input[name="surveyType"]:checked')?.value || 'survey';
					const weightControl = document.createElement('div');
					weightControl.style.cssText = 'display: flex; align-items: center; gap: 4px; font-size: 12px;';
					
					const weightLabel = document.createElement('span');
					weightLabel.textContent = surveyType === 'quiz' ? 'Points:' : 'Weight:';
					weightLabel.style.color = '#6B7280';
					
					const weightInput = document.createElement('input');
					weightInput.type = 'number';
					weightInput.value = surveyType === 'quiz' ? (q.points?.[optIndex] || 1) : (q.weights?.[optIndex] || 1);
					weightInput.style.cssText = 'width: 50px; padding: 2px 4px; border: 1px solid #D1D5DB; border-radius: 3px; font-size: 12px;';
					weightInput.step = surveyType === 'quiz' ? '1' : '0.1';
					weightInput.addEventListener('input', () => {
						if (surveyType === 'quiz') {
							if (!q.points) q.points = {};
							q.points[optIndex] = parseInt(weightInput.value) || 1;
						} else {
							if (!q.weights) q.weights = {};
							q.weights[optIndex] = parseFloat(weightInput.value) || 1;
						}
					});
					
					weightControl.appendChild(weightLabel);
					weightControl.appendChild(weightInput);
					
					optDiv.appendChild(optText);
					optDiv.appendChild(weightControl);
					body.appendChild(optDiv);
				});
			} else {
				const input = document.createElement('input');
				input.type = 'text';
				input.placeholder = 'Type your answer here...';
				input.disabled = true;
				input.style.cssText = 'width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 4px; background: #f9fafb;';
				body.appendChild(input);
				
				// Weight/Points control for text questions
				const surveyType = document.querySelector('input[name="surveyType"]:checked')?.value || 'survey';
				const textControls = document.createElement('div');
				textControls.style.cssText = 'margin-top: 10px; padding: 8px; background: rgba(15,98,254,0.05); border-radius: 6px; display: flex; align-items: center; gap: 8px;';
				
				const weightLabel = document.createElement('label');
				weightLabel.textContent = surveyType === 'quiz' ? 'Points:' : 'Weight:';
				weightLabel.style.cssText = 'font-size: 14px; color: #374151;';
				
				const weightInput = document.createElement('input');
				weightInput.type = 'number';
				weightInput.value = surveyType === 'quiz' ? (q.questionPoints || 1) : (q.questionWeight || 1);
				weightInput.style.cssText = 'width: 60px; padding: 4px 8px; border: 1px solid #D1D5DB; border-radius: 4px;';
				weightInput.step = surveyType === 'quiz' ? '1' : '0.1';
				weightInput.addEventListener('input', () => {
					if (surveyType === 'quiz') {
						q.questionPoints = parseInt(weightInput.value) || 1;
					} else {
						q.questionWeight = parseFloat(weightInput.value) || 1;
					}
				});
				
				textControls.appendChild(weightLabel);
				textControls.appendChild(weightInput);
				body.appendChild(textControls);
			}
			
			// Required toggle
			const requiredContainer = document.createElement('div');
			requiredContainer.style.cssText = 'margin-top: 12px; display: flex; align-items: center; gap: 8px;';
			
			const requiredToggle = document.createElement('input');
			requiredToggle.type = 'checkbox';
			requiredToggle.checked = q.required || false;
			requiredToggle.id = `required_${q.id}`;
			requiredToggle.addEventListener('change', () => {
				q.required = requiredToggle.checked;
			});
			
			const requiredLabel = document.createElement('label');
			requiredLabel.htmlFor = `required_${q.id}`;
			requiredLabel.textContent = 'Required';
			requiredLabel.style.cssText = 'font-size: 14px; color: #374151; cursor: pointer;';
			
			requiredContainer.appendChild(requiredToggle);
			requiredContainer.appendChild(requiredLabel);
			
			card.appendChild(header);
			card.appendChild(body);
			card.appendChild(requiredContainer);
			container.appendChild(card);
		});
		console.log('Rendered', window.questions.length, 'questions with full controls');
	} else {
		console.log('No questions to render');
	}
}
		// Add editor functions for imported questions
		function showTitleEditor(titleDisplay, q) {
			const input = document.createElement('input');
			input.type = 'text';
			input.value = q.title;
			input.style.cssText = titleDisplay.style.cssText + '; border: 2px solid #3B82F6;';
			
			titleDisplay.replaceWith(input);
			input.focus();
			input.select();
			
			const save = () => {
				q.title = input.value || 'Untitled Question';
				titleDisplay.textContent = q.title;
				input.replaceWith(titleDisplay);
			};
			
			input.addEventListener('blur', save);
			input.addEventListener('keydown', (e) => {
				if (e.key === 'Enter') save();
				if (e.key === 'Escape') {
					titleDisplay.textContent = q.title;
					input.replaceWith(titleDisplay);
				}
			});
		}
		
		function showDescriptionEditor(descDisplay, q) {
			const textarea = document.createElement('textarea');
			textarea.value = q.description || '';
			textarea.placeholder = 'Add description (optional)';
			textarea.style.cssText = 'width: 100%; min-height: 60px; padding: 8px; border: 2px solid #3B82F6; border-radius: 4px; font-size: 14px; resize: vertical;';
			
			descDisplay.replaceWith(textarea);
			textarea.focus();
			
			const save = () => {
				q.description = textarea.value.trim();
				descDisplay.textContent = q.description || 'Click to add description (optional)';
				descDisplay.style.color = q.description ? '#374151' : '#9CA3AF';
				descDisplay.style.fontStyle = q.description ? 'normal' : 'italic';
				textarea.replaceWith(descDisplay);
			};
			
			textarea.addEventListener('blur', save);
			textarea.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					descDisplay.textContent = q.description || 'Click to add description (optional)';
					textarea.replaceWith(descDisplay);
				}
			});
		}
		
		function showQuestionSettings(q) {
			// Create a simple settings modal
			const modal = document.createElement('div');
			modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;';
			
			const content = document.createElement('div');
			content.style.cssText = 'background: white; padding: 24px; border-radius: 8px; width: 400px; max-width: 90vw;';
			
			const title = document.createElement('h3');
			title.textContent = 'Question Settings';
			title.style.cssText = 'margin: 0 0 16px 0; font-size: 18px; font-weight: 600;';
			
			const typeLabel = document.createElement('label');
			typeLabel.textContent = 'Question Type:';
			typeLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500;';
			
			const typeSelect = document.createElement('select');
			typeSelect.style.cssText = 'width: 100%; padding: 8px; border: 1px solid #D1D5DB; border-radius: 4px; margin-bottom: 16px;';
			const types = ['short', 'long', 'radio', 'checkbox', 'dropdown', 'rating', 'date', 'number'];
			types.forEach(type => {
				const option = document.createElement('option');
				option.value = type;
				option.textContent = type.charAt(0).toUpperCase() + type.slice(1);
				if (type === q.type) option.selected = true;
				typeSelect.appendChild(option);
			});
			
			const buttons = document.createElement('div');
			buttons.style.cssText = 'display: flex; gap: 8px; justify-content: flex-end;';
			
			const cancelBtn = document.createElement('button');
			cancelBtn.textContent = 'Cancel';
			cancelBtn.style.cssText = 'padding: 8px 16px; border: 1px solid #D1D5DB; background: white; border-radius: 4px; cursor: pointer;';
			cancelBtn.onclick = () => modal.remove();
			
			const saveBtn = document.createElement('button');
			saveBtn.textContent = 'Save';
			saveBtn.style.cssText = 'padding: 8px 16px; border: none; background: #3B82F6; color: white; border-radius: 4px; cursor: pointer;';
			saveBtn.onclick = () => {
				q.type = typeSelect.value;
				modal.remove();
				manuallyRenderQuestions(); // Re-render to show changes
			};
			
			buttons.appendChild(cancelBtn);
			buttons.appendChild(saveBtn);
			
			content.appendChild(title);
			content.appendChild(typeLabel);
			content.appendChild(typeSelect);
			content.appendChild(buttons);
			modal.appendChild(content);
			
			// Close on background click
			modal.addEventListener('click', (e) => {
				if (e.target === modal) modal.remove();
			});
			
			document.body.appendChild(modal);
		}
	</script>
</body>
</html>
