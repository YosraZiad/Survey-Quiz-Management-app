// Field type definitions
const FIELD_TYPES = [
	{ key: 'short', title: 'Short Answer', sub: 'Single line text input' },
	{ key: 'long', title: 'Long Answer', sub: 'Multi-line text input' },
	{ key: 'radio', title: 'Multiple Choice', sub: 'Select one option' },
	{ key: 'checkbox', title: 'Checkboxes', sub: 'Select multiple options' },
	{ key: 'dropdown', title: 'Dropdown', sub: 'Menu selection' },
	{ key: 'rating', title: 'Rating Scale', sub: '1-5 stars' },
	{ key: 'date', title: 'Date', sub: 'Date picker' },
	{ key: 'number', title: 'Number', sub: 'Numeric input' }
];

const fieldList = document.getElementById('fieldList');
const dropzone = document.getElementById('dropzone');
const addFirstBtn = document.getElementById('addFirstBtn');
const emptyState = document.getElementById('emptyState');

let questions = [];
let currentSurveyType = 'survey'; // 'survey' or 'quiz'

// Initialize global questions array immediately
if (typeof window.questions === 'undefined') {
    window.questions = [];
}
questions = window.questions;

function createFieldCard(def) {
	const card = document.createElement('button');
	card.className = 'field-card';
	card.setAttribute('draggable', 'true');
	card.dataset.type = def.key;
	card.innerHTML = `
		<div class="field-card-title">
			<span>${def.title}</span>
			<span class="field-add">+</span>
		</div>
		<div class="field-card-sub">${def.sub}</div>
	`;

	card.addEventListener('click', () => addQuestion(def.key));
	card.addEventListener('dragstart', e => {
		e.dataTransfer.setData('text/plain', def.key);
	});
	return card;
}

function renderFieldList() {
	FIELD_TYPES.forEach(ft => fieldList.appendChild(createFieldCard(ft)));
}

function ensureQuestionsContainer() {
	let container = dropzone.querySelector('.questions');
	if (!container) {
		container = document.createElement('div');
		container.className = 'questions';
		dropzone.innerHTML = '';
		dropzone.appendChild(container);
	}
	return container;
}

function addQuestion(type) {
	const id = `q_${Date.now()}_${Math.random().toString(36).slice(2, 6)}`;
	const question = { 
		id, 
		type, 
		title: defaultTitle(type), 
		description: '',
		options: defaultOptions(type),
		correctAnswer: null, // For quizzes
		weights: {}, // For surveys - weight for each option
		points: {}, // For quizzes - points for each option
		questionPoints: 1, // Default points for the question itself
		questionWeight: 1.0, // Default weight for the question itself
		required: false
	};
	
	// Initialize weights and points for options
	if (question.options.length > 0) {
		question.options.forEach((_, idx) => {
			question.weights[idx] = 1;
			question.points[idx] = 1;
		});
	}
	
	// Ensure global questions array is used
	if (typeof window.questions === 'undefined') {
		window.questions = [];
	}
	
	// Initialize all required fields for validation
	if (!question.description) question.description = '';
	if (typeof question.required === 'undefined') question.required = false;
	if (!question.weights) question.weights = {};
	if (!question.points) question.points = {};
	if (typeof question.questionPoints === 'undefined') question.questionPoints = 1;
	if (typeof question.questionWeight === 'undefined') question.questionWeight = 1.0;
	
	window.questions.push(question);
	questions = window.questions; // Sync local reference
	
	console.log('Added question:', question);
	console.log('Total questions:', questions.length);
	
	renderQuestions();
}

function defaultTitle(type) {
	switch (type) {
		case 'short': return 'Short text question';
		case 'long': return 'Long text question';
		case 'radio': return 'Choose one answer';
		case 'checkbox': return 'Select options';
		case 'dropdown': return 'Choose from list';
		case 'rating': return 'Rate the experience';
		case 'date': return 'Pick a date';
		case 'number': return 'Enter a number';
		default: return 'Question';
	}
}

function defaultOptions(type) {
	if (type === 'radio' || type === 'checkbox' || type === 'dropdown') {
		return ['الخيار 1', 'الخيار 2'];
	}
	return [];
}

function renderQuestions() {
	// Ensure we're using the global questions array and sync
	if (typeof window.questions !== 'undefined') {
		questions = window.questions;
	} else {
		window.questions = questions || [];
	}
	
	// Validate all questions have required fields
	questions.forEach(q => {
		if (!q.description) q.description = '';
		if (typeof q.required === 'undefined') q.required = false;
		if (!q.weights) q.weights = {};
		if (!q.points) q.points = {};
		if (typeof q.questionPoints === 'undefined') q.questionPoints = 1;
		if (typeof q.questionWeight === 'undefined') q.questionWeight = 1.0;
	});
	
	const container = ensureQuestionsContainer();
	container.innerHTML = '';
	
	// Check if we have questions to render
	if (questions.length === 0) {
		// Show empty state
		container.innerHTML = '<div class="empty-state">No questions added yet. Add questions from the field types on the right.</div>';
		return;
	}
	
	questions.forEach(q => container.appendChild(renderQuestion(q)));
	enableReorder(container);
	updateSurveyStats();
}

function renderQuestion(q) {
	const card = document.createElement('div');
	card.className = 'q-card';
	card.dataset.id = q.id;
	card.setAttribute('draggable', 'true');

	const header = document.createElement('div');
	header.className = 'q-header';

	const handle = document.createElement('div');
	handle.className = 'q-handle';
	handle.title = 'Drag to reorder';
	handle.textContent = '⋮⋮';

	// Title container with hover editing
	const titleContainer = document.createElement('div');
	titleContainer.className = 'title-container';
	
	const titleDisplay = document.createElement('div');
	titleDisplay.className = 'q-title-display';
	titleDisplay.textContent = q.title;
	titleDisplay.addEventListener('click', () => {
		showTitleEditor(titleDisplay, q);
	});
	
	// Question type display
	const typeDisplay = document.createElement('span');
	typeDisplay.className = 'q-type';
	typeDisplay.textContent = q.type;
	typeDisplay.style.cssText = 'font-size: 12px; color: var(--muted); margin-left: 8px; display: none;';
	titleContainer.appendChild(titleDisplay);
	titleContainer.appendChild(typeDisplay);

	// Description container
	const descContainer = document.createElement('div');
	descContainer.className = 'desc-container';
	descContainer.style.marginTop = '8px';
	
	const descDisplay = document.createElement('div');
	descDisplay.className = 'q-desc-display';
	descDisplay.textContent = q.description || 'Click to add description (optional)';
	descDisplay.style.color = q.description ? '#374151' : '#9CA3AF';
	descDisplay.style.fontSize = '14px';
	descDisplay.style.fontStyle = q.description ? 'normal' : 'italic';
	descDisplay.addEventListener('click', () => {
		showDescriptionEditor(descDisplay, q);
	});
	
	descContainer.appendChild(descDisplay);

	const actions = document.createElement('div');
	actions.className = 'q-actions';
	
	const settingsBtn = document.createElement('button');
	settingsBtn.className = 'icon-btn';
	settingsBtn.title = 'Settings';
	settingsBtn.textContent = '⚙️';
	settingsBtn.addEventListener('click', () => showQuestionSettings(q));
	
	const removeBtn = document.createElement('button');
	removeBtn.className = 'icon-btn';
	removeBtn.title = 'Delete';
	removeBtn.textContent = '🗑️';
	removeBtn.addEventListener('click', () => removeQuestion(q.id));
	
	actions.appendChild(settingsBtn);
	actions.appendChild(removeBtn);

	header.appendChild(handle);
	header.appendChild(titleContainer);
	header.appendChild(descContainer);
	header.appendChild(actions);

	const body = document.createElement('div');
	body.className = 'q-body';

	switch (q.type) {
		case 'short': {
			const input = document.createElement('input');
			input.type = 'text';
			input.placeholder = 'Type your answer here...';
			body.appendChild(input);
			
			// Add weight/points controls for text questions
			const textControls = document.createElement('div');
			textControls.className = 'text-controls';
			textControls.style.marginTop = '10px';
			textControls.style.padding = '8px';
			textControls.style.background = 'rgba(15,98,254,0.05)';
			textControls.style.borderRadius = '6px';
			
			const weightLabel = document.createElement('label');
			const surveyType = document.querySelector('input[name="surveyType"]:checked').value;
			weightLabel.innerHTML = surveyType === 'quiz' ? 'Points: ' : 'Weight: ';
			const weightInput = document.createElement('input');
			weightInput.type = 'number';
			weightInput.value = surveyType === 'quiz' ? (q.questionPoints || 1) : (q.questionWeight || 1);
			weightInput.style.marginLeft = '8px';
			weightInput.style.width = '60px';
			weightInput.step = surveyType === 'quiz' ? '1' : '0.1';
			weightInput.addEventListener('input', () => {
				if (surveyType === 'quiz') {
					q.questionPoints = parseInt(weightInput.value) || 1;
				} else {
					q.questionWeight = parseFloat(weightInput.value) || 1;
				}
				updateSurveyStats();
			});
			weightLabel.appendChild(weightInput);
			textControls.appendChild(weightLabel);
			
			body.appendChild(textControls);
			break;
		}
		case 'long': {
			const textarea = document.createElement('textarea');
			textarea.placeholder = 'Type your detailed answer here...';
			textarea.rows = 4;
			body.appendChild(textarea);
			
			// Add weight/points controls for text questions
			const textControls = document.createElement('div');
			textControls.className = 'text-controls';
			textControls.style.marginTop = '10px';
			textControls.style.padding = '8px';
			textControls.style.background = 'rgba(15,98,254,0.05)';
			textControls.style.borderRadius = '6px';
			
			const weightLabel = document.createElement('label');
			const surveyType = document.querySelector('input[name="surveyType"]:checked').value;
			weightLabel.innerHTML = surveyType === 'quiz' ? 'Points: ' : 'Weight: ';
			const weightInput = document.createElement('input');
			weightInput.type = 'number';
			weightInput.value = surveyType === 'quiz' ? (q.questionPoints || 1) : (q.questionWeight || 1);
			weightInput.style.marginLeft = '8px';
			weightInput.style.width = '60px';
			weightInput.step = surveyType === 'quiz' ? '1' : '0.1';
			weightInput.addEventListener('input', () => {
				if (surveyType === 'quiz') {
					q.questionPoints = parseInt(weightInput.value) || 1;
				} else {
					q.questionWeight = parseFloat(weightInput.value) || 1;
				}
				updateSurveyStats();
			});
			weightLabel.appendChild(weightInput);
			textControls.appendChild(weightLabel);
			
			body.appendChild(textControls);
			break;
		}
		case 'radio':
		case 'checkbox':
		case 'dropdown': {
			const optionsWrap = document.createElement('div');
			q.options.forEach((opt, idx) => {
				const row = document.createElement('div');
				row.className = 'option-row';
				
				// Option text input
				const input = document.createElement('input');
				input.type = 'text';
				input.value = opt;
				input.className = 'option-input';
				
				// Make input editable with proper event handling
				input.addEventListener('input', function() { 
					q.options[idx] = this.value;
					window.questions = questions;
					console.log('Option updated:', idx, this.value);
				});
				
				input.addEventListener('change', function() { 
					q.options[idx] = this.value;
					window.questions = questions;
					updatePreview();
					console.log('Option saved:', idx, this.value);
				});
				
				// Quiz: Correct answer checkbox and points input
				if (currentSurveyType === 'quiz') {
					const correctCheck = document.createElement('input');
					correctCheck.type = 'radio';
					correctCheck.name = `correct_${q.id}`;
					correctCheck.checked = q.correctAnswer === idx;
					correctCheck.addEventListener('change', () => {
						if (correctCheck.checked) {
							q.correctAnswer = idx;
							updatePreview();
						}
					});
					const correctLabel = document.createElement('label');
					correctLabel.textContent = 'Correct';
					correctLabel.style.fontSize = '12px';
					correctLabel.style.color = 'var(--muted)';
					correctLabel.insertBefore(correctCheck, correctLabel.firstChild);
					
					// Points input for each option
					const pointsInput = document.createElement('input');
					pointsInput.type = 'number';
					pointsInput.placeholder = 'Points';
					pointsInput.value = q.points[idx] || '';
					pointsInput.style.width = '60px';
					pointsInput.style.marginLeft = '8px';
					pointsInput.addEventListener('input', () => {
						q.points[idx] = parseInt(pointsInput.value) || 0;
						updateSurveyStats();
						updatePreview();
					});
					const pointsLabel = document.createElement('label');
					pointsLabel.textContent = 'Points:';
					pointsLabel.style.fontSize = '12px';
					pointsLabel.style.color = 'var(--muted)';
					pointsLabel.style.marginLeft = '8px';
					pointsLabel.appendChild(pointsInput);
					
					row.appendChild(correctLabel);
					row.appendChild(pointsLabel);
				}
				
				// Survey: Weight input
				if (currentSurveyType === 'survey') {
					const weightInput = document.createElement('input');
					weightInput.type = 'number';
					weightInput.placeholder = 'Weight';
					weightInput.value = q.weights[idx] || '';
					weightInput.style.width = '80px';
					weightInput.addEventListener('input', () => {
						q.weights[idx] = parseFloat(weightInput.value) || 0;
						updateSurveyStats();
						updatePreview();
					});
					row.appendChild(weightInput);
				}
				
				const del = document.createElement('button');
				del.className = 'icon-btn';
				del.textContent = '−';
				del.title = 'Delete option';
				del.addEventListener('click', () => { q.options.splice(idx, 1); renderQuestions(); });
				
				row.appendChild(input);
				row.appendChild(del);
				optionsWrap.appendChild(row);
			});
			const addOption = document.createElement('button');
			addOption.className = 'btn add-option';
			addOption.textContent = '+ Add option';
			addOption.addEventListener('click', () => { 
				q.options.push(`الخيار ${q.options.length + 1}`); 
				// Update weights and points for new option
				const newIdx = q.options.length - 1;
				q.weights[newIdx] = 1;
				q.points[newIdx] = 1;
				window.questions = questions;
				renderQuestions(); 
			});
			body.appendChild(optionsWrap);
			body.appendChild(addOption);
			break;
		}
		case 'rating': {
			const stars = document.createElement('div');
			for (let i = 1; i <= 5; i++) {
				const s = document.createElement('span');
				s.textContent = '★';
				s.style.color = '#f59e0b';
				s.style.fontSize = '20px';
				s.style.marginInline = '2px';
				stars.appendChild(s);
			}
			body.appendChild(stars);
			
			// Add weight/correct answer controls for rating
			const ratingControls = document.createElement('div');
			ratingControls.className = 'rating-controls';
			ratingControls.style.marginTop = '10px';
			ratingControls.style.padding = '8px';
			ratingControls.style.background = 'rgba(15,98,254,0.05)';
			ratingControls.style.borderRadius = '6px';
			
			if (currentSurveyType === 'quiz') {
				const correctLabel = document.createElement('label');
				correctLabel.innerHTML = 'Correct Rating: ';
				const correctSelect = document.createElement('select');
				correctSelect.style.marginLeft = '8px';
				for (let i = 1; i <= 5; i++) {
					const option = document.createElement('option');
					option.value = i;
					option.textContent = i + ' stars';
					correctSelect.appendChild(option);
				}
				correctSelect.value = q.correctAnswer || 1;
				correctSelect.addEventListener('change', () => {
					q.correctAnswer = parseInt(correctSelect.value);
				});
				correctLabel.appendChild(correctSelect);
				ratingControls.appendChild(correctLabel);
			} else if (currentSurveyType === 'survey') {
				const weightLabel = document.createElement('label');
				weightLabel.innerHTML = 'Weight per star: ';
				const weightInput = document.createElement('input');
				weightInput.type = 'number';
				weightInput.value = q.weights[0] || 1;
				weightInput.style.marginLeft = '8px';
				weightInput.style.width = '60px';
				weightInput.addEventListener('input', () => {
					q.weights[0] = parseFloat(weightInput.value) || 1;
					updateSurveyStats();
				});
				weightLabel.appendChild(weightInput);
				ratingControls.appendChild(weightLabel);
			}
			
			body.appendChild(ratingControls);
			break;
		}
		case 'date': {
			const input = document.createElement('input');
			input.type = 'date';
			body.appendChild(input);
			
			// Add weight/correct answer controls for date
			const dateControls = document.createElement('div');
			dateControls.className = 'date-controls';
			dateControls.style.marginTop = '10px';
			dateControls.style.padding = '8px';
			dateControls.style.background = 'rgba(15,98,254,0.05)';
			dateControls.style.borderRadius = '6px';
			
			if (currentSurveyType === 'quiz') {
				const correctLabel = document.createElement('label');
				correctLabel.innerHTML = 'Correct Date: ';
				const correctDate = document.createElement('input');
				correctDate.type = 'date';
				correctDate.value = q.correctAnswer || '';
				correctDate.style.marginLeft = '8px';
				correctDate.addEventListener('change', () => {
					q.correctAnswer = correctDate.value;
				});
				correctLabel.appendChild(correctDate);
				dateControls.appendChild(correctLabel);
			} else if (currentSurveyType === 'survey') {
				const weightLabel = document.createElement('label');
				weightLabel.innerHTML = 'Weight: ';
				const weightInput = document.createElement('input');
				weightInput.type = 'number';
				weightInput.value = q.weights[0] || 1;
				weightInput.style.marginLeft = '8px';
				weightInput.style.width = '60px';
				weightInput.addEventListener('input', () => {
					q.weights[0] = parseFloat(weightInput.value) || 1;
					updateSurveyStats();
				});
				weightLabel.appendChild(weightInput);
				dateControls.appendChild(weightLabel);
			}
			
			body.appendChild(dateControls);
			break;
		}
		case 'number': {
			const input = document.createElement('input');
			input.type = 'number';
			input.placeholder = '0';
			body.appendChild(input);
			
			// Add weight/correct answer controls for number
			const numberControls = document.createElement('div');
			numberControls.className = 'number-controls';
			numberControls.style.marginTop = '10px';
			numberControls.style.padding = '8px';
			numberControls.style.background = 'rgba(15,98,254,0.05)';
			numberControls.style.borderRadius = '6px';
			
			if (currentSurveyType === 'quiz') {
				const correctLabel = document.createElement('label');
				correctLabel.innerHTML = 'Correct Answer: ';
				const correctNumber = document.createElement('input');
				correctNumber.type = 'number';
				correctNumber.value = q.correctAnswer || 0;
				correctNumber.style.marginLeft = '8px';
				correctNumber.style.width = '80px';
				correctNumber.addEventListener('input', () => {
					q.correctAnswer = parseFloat(correctNumber.value) || 0;
				});
				correctLabel.appendChild(correctNumber);
				numberControls.appendChild(correctLabel);
			} else if (currentSurveyType === 'survey') {
				const weightLabel = document.createElement('label');
				weightLabel.innerHTML = 'Weight: ';
				const weightInput = document.createElement('input');
				weightInput.type = 'number';
				weightInput.value = q.weights[0] || 1;
				weightInput.style.marginLeft = '8px';
				weightInput.style.width = '60px';
				weightInput.addEventListener('input', () => {
					q.weights[0] = parseFloat(weightInput.value) || 1;
					updateSurveyStats();
				});
				weightLabel.appendChild(weightInput);
				numberControls.appendChild(weightLabel);
			}
			
			body.appendChild(numberControls);
			break;
		}
	}

	const footer = document.createElement('div');
	footer.className = 'q-footer';
	const toggle = document.createElement('div');
	toggle.className = 'toggle';
	if (q.required) {
		toggle.classList.add('on');
	}
	const reqLabel = document.createElement('span');
	reqLabel.textContent = 'Required';
	toggle.addEventListener('click', () => {
		toggle.classList.toggle('on');
		q.required = toggle.classList.contains('on');
	});
	footer.appendChild(toggle);
	footer.appendChild(reqLabel);

	card.appendChild(header);
	card.appendChild(body);
	card.appendChild(footer);

	card.addEventListener('dragstart', e => {
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/plain', q.id);
	});
	return card;
}

function removeQuestion(id) {
	questions = questions.filter(q => q.id !== id);
	if (questions.length === 0) {
		dropzone.innerHTML = '';
		dropzone.appendChild(emptyState);
	}
	renderQuestions();
}

// Drag and drop for creating questions
['dragover', 'dragenter'].forEach(eventName => {
	dropzone.addEventListener(eventName, e => {
		e.preventDefault();
		dropzone.classList.add('dragover');
	});
});

dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));

dropzone.addEventListener('drop', e => {
	e.preventDefault();
	dropzone.classList.remove('dragover');
	const type = e.dataTransfer.getData('text/plain');
	
	// Only add question if it's a field type, not a question ID
	if (type && FIELD_TYPES.some(ft => ft.key === type)) {
		addQuestion(type);
	}
});

addFirstBtn.addEventListener('click', () => addQuestion('radio'));

// Update survey statistics
function updateSurveyStats() {
	const totalQuestionsEl = document.getElementById('totalQuestions');
	const totalPointsEl = document.getElementById('totalPoints');
	const maxWeightEl = document.getElementById('maxWeight');
	const pointsCardEl = document.getElementById('pointsCard');
	const weightsCardEl = document.getElementById('weightsCard');
	
	if (!totalQuestionsEl) return;
	
	// Update total questions
	totalQuestionsEl.textContent = questions.length;
	
	if (currentSurveyType === 'quiz') {
		// Show points card, hide weights card
		pointsCardEl.style.display = 'flex';
		weightsCardEl.style.display = 'none';
		
		// Calculate total points (sum of all option points for each question)
		const totalPoints = questions.reduce((sum, q) => {
			if (q.options && q.options.length > 0) {
				// For questions with options, sum the points of all options
				const questionPoints = Object.values(q.points || {}).reduce((qSum, points) => qSum + (points || 0), 0);
				return sum + questionPoints;
			} else {
				// For text questions, use the question points
				return sum + (q.questionPoints || 1);
			}
		}, 0);
		totalPointsEl.textContent = totalPoints;
		
	} else if (currentSurveyType === 'survey') {
		// Show weights card, hide points card
		pointsCardEl.style.display = 'none';
		weightsCardEl.style.display = 'flex';
		
		// Calculate max possible weight
		let maxWeight = 0;
		questions.forEach(q => {
			if (q.options && q.options.length > 0) {
				const questionMaxWeight = Math.max(...Object.values(q.weights || {}));
				maxWeight += questionMaxWeight || 0;
			} else {
				// For text questions, add the question weight
				maxWeight += q.questionWeight || 1;
			}
		});
		maxWeightEl.textContent = maxWeight.toFixed(1);
	}
}

// Survey type change handler
document.addEventListener('DOMContentLoaded', () => {
	const typeRadios = document.querySelectorAll('input[name="surveyType"]');
	typeRadios.forEach(radio => {
		radio.addEventListener('change', (e) => {
			currentSurveyType = e.target.value;
			// Re-render questions to show/hide correct answers or weights
			renderQuestions();
		});
	});
});

// Title editor function
function showTitleEditor(displayElement, question) {
	// Make the display element editable directly
	displayElement.contentEditable = true;
	displayElement.className = 'q-title-display editing';
	displayElement.focus();
	
	// Select all text for easy editing
	const range = document.createRange();
	range.selectNodeContents(displayElement);
	const selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
	
	// Create toolbar
	const toolbar = document.createElement('div');
	toolbar.className = 'text-toolbar';
	
	// Bold button
	const boldBtn = document.createElement('button');
	boldBtn.innerHTML = '<b>B</b>';
	boldBtn.title = 'Bold';
	boldBtn.addEventListener('click', (e) => {
		e.preventDefault();
		document.execCommand('bold');
		displayElement.focus();
	});
	
	// Italic button
	const italicBtn = document.createElement('button');
	italicBtn.innerHTML = '<i>I</i>';
	italicBtn.title = 'Italic';
	italicBtn.addEventListener('click', (e) => {
		e.preventDefault();
		document.execCommand('italic');
		displayElement.focus();
	});
	
	// Align left
	const alignLeftBtn = document.createElement('button');
	alignLeftBtn.innerHTML = '⬅️';
	alignLeftBtn.title = 'Align Left';
	alignLeftBtn.addEventListener('click', (e) => {
		e.preventDefault();
		document.execCommand('justifyLeft');
		displayElement.focus();
	});
	
	// Align center
	const alignCenterBtn = document.createElement('button');
	alignCenterBtn.innerHTML = '↔️';
	alignCenterBtn.title = 'Align Center';
	alignCenterBtn.addEventListener('click', (e) => {
		e.preventDefault();
		document.execCommand('justifyCenter');
		displayElement.focus();
	});
	
	// Align right
	const alignRightBtn = document.createElement('button');
	alignRightBtn.innerHTML = '➡️';
	alignRightBtn.title = 'Align Right';
	alignRightBtn.addEventListener('click', (e) => {
		e.preventDefault();
		document.execCommand('justifyRight');
		displayElement.focus();
	});
	
	toolbar.appendChild(boldBtn);
	toolbar.appendChild(italicBtn);
	toolbar.appendChild(alignLeftBtn);
	toolbar.appendChild(alignCenterBtn);
	toolbar.appendChild(alignRightBtn);
	
	// Insert toolbar after the title element
	displayElement.parentNode.insertBefore(toolbar, displayElement.nextSibling);
	
	// Save changes on blur or enter
	const saveChanges = () => {
		question.title = displayElement.textContent;
		displayElement.contentEditable = false;
		displayElement.className = 'q-title-display';
		toolbar.remove();
	};
	
	displayElement.addEventListener('blur', saveChanges);
	displayElement.addEventListener('keydown', (e) => {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			saveChanges();
		}
		if (e.key === 'Escape') {
			displayElement.textContent = question.title;
			saveChanges();
		}
	});
}

// Description editor function
function showDescriptionEditor(displayElement, question) {
	// Make the display element editable directly
	displayElement.contentEditable = true;
	displayElement.className = 'q-desc-display editing';
	displayElement.style.color = '#374151';
	displayElement.style.fontStyle = 'normal';
	
	// Clear placeholder text if it's the default
	if (displayElement.textContent === 'Click to add description (optional)') {
		displayElement.textContent = '';
	}
	
	displayElement.focus();
	
	// Select all text for easy editing
	const range = document.createRange();
	range.selectNodeContents(displayElement);
	const selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
	
	// Save changes on blur or enter
	const saveChanges = () => {
		const newDescription = displayElement.textContent.trim();
		question.description = newDescription;
		
		if (newDescription) {
			displayElement.textContent = newDescription;
			displayElement.style.color = '#374151';
			displayElement.style.fontStyle = 'normal';
		} else {
			displayElement.textContent = 'Click to add description (optional)';
			displayElement.style.color = '#9CA3AF';
			displayElement.style.fontStyle = 'italic';
		}
		
		displayElement.contentEditable = false;
		displayElement.className = 'q-desc-display';
	};
	
	displayElement.addEventListener('keydown', (e) => {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			saveChanges();
		}
		if (e.key === 'Escape') {
			displayElement.textContent = question.description || 'Click to add description (optional)';
			saveChanges();
		}
	});
	
	displayElement.addEventListener('blur', saveChanges);
}

// Question settings function
function showQuestionSettings(q) {
	// Create modal overlay
	const overlay = document.createElement('div');
	overlay.className = 'modal-overlay';
	overlay.style.cssText = `
		position: fixed; top: 0; left: 0; right: 0; bottom: 0;
		background: rgba(0,0,0,0.5); z-index: 1000;
		display: flex; align-items: center; justify-content: center;
	`;

	// Create modal
	const modal = document.createElement('div');
	modal.className = 'modal';
	modal.style.cssText = `
		background: white; border-radius: 12px; padding: 24px;
		max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;
	`;

	// Modal header
	const header = document.createElement('div');
	header.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;';
	const title = document.createElement('h3');
	title.textContent = 'Question Settings';
	title.style.margin = '0';
	const closeBtn = document.createElement('button');
	closeBtn.textContent = '×';
	closeBtn.style.cssText = 'background: none; border: none; font-size: 24px; cursor: pointer; color: #666;';
	closeBtn.addEventListener('click', () => document.body.removeChild(overlay));
	header.appendChild(title);
	header.appendChild(closeBtn);

	// Question type selector
	const typeSection = document.createElement('div');
	typeSection.style.marginBottom = '20px';
	const typeLabel = document.createElement('label');
	typeLabel.textContent = 'Question Type:';
	typeLabel.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 600;';
	const typeSelect = document.createElement('select');
	typeSelect.style.cssText = 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;';
	
	FIELD_TYPES.forEach(type => {
		const option = document.createElement('option');
		option.value = type.key;
		option.textContent = type.title;
		if (type.key === q.type) option.selected = true;
		typeSelect.appendChild(option);
	});

	typeSelect.addEventListener('change', () => {
		const oldType = q.type;
		const newType = typeSelect.value;
		
		// Update question type and reset options
		q.type = newType;
		q.options = defaultOptions(newType);
		
		// Reset weights and points for new options
		q.weights = {};
		q.points = {};
		q.correctAnswer = null;
		
		// Initialize weights and points for new options
		if (q.options.length > 0) {
			q.options.forEach((_, idx) => {
				q.weights[idx] = 1;
				q.points[idx] = 1;
			});
		}
		
		// Update the question in the global array
		const questionIndex = questions.findIndex(question => question.id === q.id);
		if (questionIndex !== -1) {
			questions[questionIndex] = { ...q };
			window.questions = questions;
		}
		
		console.log(`Question type changed from ${oldType} to ${newType}`);
		
		// Re-render questions immediately to show the new type in builder
		renderQuestions();
		
		// Save the survey to update the database
		saveSurvey({ publish: false, redirectToPreview: false }).then(() => {
			console.log(`Question type change saved successfully`);
		}).catch(err => {
			console.error('Failed to save after type change:', err);
		});
	});

	typeSection.appendChild(typeLabel);
	typeSection.appendChild(typeSelect);

	// Save button
	const saveBtn = document.createElement('button');
	saveBtn.textContent = 'Save Changes';
	saveBtn.className = 'btn primary';
	saveBtn.style.cssText = 'width: 100%; margin-top: 20px;';
	saveBtn.addEventListener('click', () => {
		renderQuestions();
		updatePreview();
		document.body.removeChild(overlay);
	});

	modal.appendChild(header);
	modal.appendChild(typeSection);
	modal.appendChild(saveBtn);
	overlay.appendChild(modal);
	document.body.appendChild(overlay);

	// Close on overlay click
	overlay.addEventListener('click', (e) => {
		if (e.target === overlay) document.body.removeChild(overlay);
	});
}

// Init
renderFieldList();

// --- Backend integration (Laravel API) ---
let currentSurveyId = null;

async function saveSurvey({ publish = false, redirectToPreview = false } = {}) {
    try {
        const titleEl = document.getElementById('surveyTitle');
        const descEl = document.getElementById('surveyDesc');
        const surveyType = document.querySelector('input[name="surveyType"]:checked')?.value || 'survey';
        
        // Validate required fields before building payload
        const title = titleEl ? titleEl.value.trim() : '';
        if (!title) {
            alert('Please enter a survey title');
            return;
        }
        
        // Check if we have questions in the DOM
        const questionCards = document.querySelectorAll('.q-card');
        if (questionCards.length === 0) {
            alert('Please add at least one question to your survey');
            return;
        }
        
        const payload = buildSurveyPayload({
            title: title,
            description: descEl ? descEl.value.trim() : '',
            type: surveyType,
            is_published: !!publish,
        });

        console.log('Sending payload:', JSON.stringify(payload, null, 2));

        const endpoint = currentSurveyId ? `/api/surveys/${currentSurveyId}` : '/api/surveys';
        const method = currentSurveyId ? 'PUT' : 'POST';
        const res = await fetch(endpoint, {
            method,
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload),
        });
        
        if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error('Save error response:', errorData);
            
            // Show detailed validation errors if available
            if (res.status === 422 && errorData.details) {
                const errorMessages = Object.entries(errorData.details)
                    .map(([field, messages]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
                    .join('\n');
                throw new Error(`Validation failed:\n${errorMessages}`);
            }
            
            throw new Error(`Save failed (${res.status}): ${errorData.error || errorData.message || 'Unknown error'}`);
        }
        
        const data = await res.json();
        currentSurveyId = data.id;

        if (publish) {
            const pubRes = await fetch(`/api/surveys/${currentSurveyId}/publish`, { 
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!pubRes.ok) {
                const pubError = await pubRes.json().catch(() => ({}));
                throw new Error(`Publish failed (${pubRes.status}): ${pubError.error || 'Unknown error'}`);
            }
            alert('Survey published successfully');
        } else {
            if (redirectToPreview) {
                window.location.href = `/preview/${currentSurveyId}`;
                return;
            }
            alert('Survey saved successfully');
        }
    } catch (err) {
        console.error('Save error:', err);
        alert(err.message || 'Save failed');
    }
}

function buildSurveyPayload({ title, description, type, is_published }) {
    // Get questions from DOM elements directly to ensure we have the latest data
    const questionCards = document.querySelectorAll('.q-card');
    const questionsFromDOM = [];
    
    questionCards.forEach((card, idx) => {
        const questionId = card.dataset.id;
        const titleEl = card.querySelector('.q-title-display');
        const descEl = card.querySelector('.q-desc-display');
        const typeEl = card.querySelector('.q-type');
        const requiredEl = card.querySelector('input[type="checkbox"]');
        
        const questionTitle = titleEl ? titleEl.textContent.trim() : 'Question';
        const questionDesc = descEl ? descEl.textContent.trim() : '';
        // Get the actual question type from the questions array instead of DOM
        const actualQuestion = questions.find(question => question.id === questionId);
        const questionType = actualQuestion ? actualQuestion.type : 'short';
        const isRequired = requiredEl ? requiredEl.checked : false;
        
        const question = {
            id: questionId && questionId.includes('q_') ? undefined : parseInt(questionId),
            title: questionTitle,
            description: questionDesc,
            type: questionType,
            required: isRequired,
            display_order: idx,
            metadata: null,
        };
        
        // Add points or weight based on survey type
        if (type === 'quiz') {
            question.points = 1;
        } else {
            question.weight = 1.0;
        }
        
        // Handle options for multi-choice questions
        if (questionType === 'radio' || questionType === 'checkbox' || questionType === 'dropdown') {
            // Get options from the actual question object instead of DOM
            const actualOptions = actualQuestion ? actualQuestion.options : [];
            question.options = actualOptions.map((label, oIdx) => ({
                label: typeof label === 'string' ? label : (label?.label || `Option ${oIdx + 1}`),
                weight: type === 'survey' ? (actualQuestion?.weights?.[oIdx] ?? 1.0) : null,
                points: type === 'quiz' ? (actualQuestion?.points?.[oIdx] ?? 1) : null,
                is_correct: type === 'quiz' ? (actualQuestion?.correctAnswer === oIdx) : false,
                display_order: oIdx,
            }));
        } else {
            question.options = [];
        }
        
        questionsFromDOM.push(question);
    });
    
    console.log('Questions from DOM:', questionsFromDOM.length);
    console.log('Questions data:', questionsFromDOM);
    
    const payload = { title, description, type, is_published, questions: questionsFromDOM };
    console.log('Built payload:', payload);
    return payload;
}

// CSV Format Help
function showCSVHelp() {
    alert(`CSV Format:\nQuestion Title, Question Type, Option1, Option2, Option3...\n\nExample:\n"What is your name?", "short"\n"Choose your age", "radio", "18-25", "26-35", "36-45"\n"Select hobbies", "checkbox", "Reading", "Sports", "Music"\n\nSupported Types: short, long, radio, checkbox, dropdown, rating, date, number`);
}

// Update preview function
function updatePreview() {
    // Save the survey automatically when preview is updated
    if (questions.length > 0) {
        saveSurvey({ publish: false, redirectToPreview: false }).catch(err => {
            console.error('Failed to auto-save:', err);
        });
    }
    console.log('Preview updated and saved');
}

// Hook buttons
(() => {
    const publishBtn = document.getElementById('publishBtn');
    if (publishBtn) publishBtn.addEventListener('click', () => saveSurvey({ publish: true }));
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) previewBtn.addEventListener('click', () => saveSurvey({ publish: false, redirectToPreview: true }));
    
    // Load existing survey into builder when ?survey=ID is present
    document.addEventListener('DOMContentLoaded', () => {
        tryLoadSurveyFromQuery();
        renderFieldList();
        
        // Setup CSV import after DOM is loaded
        setupCSVImport();
    });

})();

function setupCSVImport() {
    // CSV import handler
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
                
                const imported = [];
                
                lines.forEach((line, index) => {
                    const parts = line.split(',').map(part => part.trim().replace(/^"|"$/g, ''));
                    if (parts.length < 2) return; // Skip invalid lines
                    
                    const questionTitle = parts[0];
                    const questionType = parts[1] || 'short';
                    const options = parts.slice(2).filter(opt => opt.length > 0);
                    
                    const id = `q_${Date.now()}_${Math.random().toString(36).slice(2, 6)}_${index}`;
                    const mapped = {
                        id,
                        type: ['short', 'long', 'radio', 'checkbox', 'dropdown', 'rating', 'date', 'number'].includes(questionType) ? questionType : 'short',
                        title: questionTitle || `Question ${index + 1}`,
                        options: (questionType === 'radio' || questionType === 'checkbox' || questionType === 'dropdown') ? options : [],
                        correctAnswer: null,
                        weights: {},
                        points: {},
                        questionPoints: 1,
                        questionWeight: 1.0,
                        required: false
                    };
                    
                    // Initialize weights and points for options
                    if (mapped.options.length > 0) {
                        mapped.options.forEach((_, idx) => {
                            mapped.weights[idx] = 1;
                            mapped.points[idx] = 1;
                        });
                    }
                    
                    imported.push(mapped);
                });
                
                console.log('Imported questions:', imported);
                
                // Add imported questions to the current survey
                imported.forEach(q => questions.push(q));
                
                // Update the survey type if needed
                currentSurveyType = type;
                const typeRadio = document.querySelector(`input[name="surveyType"][value="${type}"]`);
                if (typeRadio) typeRadio.checked = true;
                
                renderQuestions();
                updateSurveyStats();
                alert(`Imported ${imported.length} questions from CSV`);
                
                // Clear the file input
                fileInput.value = '';
                
            } catch (err) {
                console.error('CSV Import error:', err);
                alert('CSV Import error: ' + err.message);
            }
        });
    } else {
        console.log('CSV Import form not found');
    }
}

async function tryLoadSurveyFromQuery(){
    try {
        const params = new URLSearchParams(location.search);
        const sid = params.get('survey');
        if (!sid) return;
        const res = await fetch(`/api/surveys/${sid}`);
        if (!res.ok) return;
        const data = await res.json();
        currentSurveyId = data.id;
        // Populate header
        const titleEl = document.getElementById('surveyTitle');
        const descEl = document.getElementById('surveyDesc');
        if (titleEl) titleEl.value = data.title || 'Untitled';
        if (descEl) descEl.value = data.description || '';
        // type
        currentSurveyType = data.type || 'survey';
        const typeRadio = document.querySelector(`input[name="surveyType"][value="${currentSurveyType}"]`);
        if (typeRadio) typeRadio.checked = true;
        // Map questions
        questions = (data.questions || []).map(q => ({
            id: q.id,
            type: q.type,
            title: q.title,
            description: q.description || '',
            required: !!q.required,
            questionPoints: q.points ?? undefined,
            questionWeight: q.weight ?? undefined,
            options: Array.isArray(q.options) ? q.options.map(o => o.label) : [],
            weights: Array.isArray(q.options) ? q.options.map(o => (o.weight ?? null)) : [],
            points: Array.isArray(q.options) ? q.options.map(o => (o.points ?? null)) : [],
            correctAnswer: (() => {
                if (currentSurveyType === 'quiz' && Array.isArray(q.options)) {
                    const idx = q.options.findIndex(o => !!o.is_correct);
                    return idx >= 0 ? idx : undefined;
                }
                return undefined;
            })()
        }));
        // Render
        renderQuestions();
    } catch (e) {
        console.warn('Failed to load survey from query', e);
    }
}

// Initialize on page load
renderFieldList();
updateSurveyStats();

function enableReorder(container) {
	const cards = Array.from(container.querySelectorAll('.q-card'));
	cards.forEach(card => {
		card.addEventListener('dragover', e => {
			e.preventDefault();
		});
		card.addEventListener('drop', e => {
			e.preventDefault();
			const draggedId = e.dataTransfer.getData('text/plain');
			const targetId = card.dataset.id;
			
			// Check if this is a field type being dragged (not a question reorder)
			if (FIELD_TYPES.some(ft => ft.key === draggedId)) {
				// This is a new field being added, don't process as reorder
				return;
			}
			
			if (!draggedId || draggedId === targetId) return;
			const from = questions.findIndex(x => x.id === draggedId);
			const to = questions.findIndex(x => x.id === targetId);
			if (from === -1 || to === -1) return;
			const [moved] = questions.splice(from, 1);
			questions.splice(to, 0, moved);
			renderQuestions();
		});
	});
}
