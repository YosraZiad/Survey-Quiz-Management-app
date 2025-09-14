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
		options: defaultOptions(type),
		correctAnswer: null, // For quizzes
		weights: {}, // For surveys - weight for each option
		points: {}, // For quizzes - points for each option
		questionPoints: 1, // Default points for the question itself
		questionWeight: 1.0 // Default weight for the question itself
	};
	questions.push(question);
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
	const container = ensureQuestionsContainer();
	container.innerHTML = '';
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
	
	titleContainer.appendChild(titleDisplay);

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
				row.className = 'option';
				
				// Option text input
				const input = document.createElement('input');
				input.type = 'text';
				input.value = opt;
				input.addEventListener('input', () => { q.options[idx] = input.value; });
				
				// Quiz: Correct answer checkbox and points input
				if (currentSurveyType === 'quiz') {
					const correctCheck = document.createElement('input');
					correctCheck.type = 'radio';
					correctCheck.name = `correct_${q.id}`;
					correctCheck.checked = q.correctAnswer === idx;
					correctCheck.addEventListener('change', () => {
						if (correctCheck.checked) q.correctAnswer = idx;
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
			addOption.addEventListener('click', () => { q.options.push(`Option ${q.options.length + 1}`); renderQuestions(); });
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
	const reqLabel = document.createElement('span');
	reqLabel.textContent = 'Required';
	toggle.addEventListener('click', () => toggle.classList.toggle('on'));
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
	if (type) addQuestion(type);
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
function showQuestionSettings(question) {
	const modal = document.createElement('div');
	modal.className = 'settings-modal';
	
	const content = document.createElement('div');
	content.className = 'settings-content';
	
	const title = document.createElement('h3');
	title.textContent = 'Question Settings';
	
	const form = document.createElement('form');
	
	// Required toggle
	const requiredDiv = document.createElement('div');
	requiredDiv.className = 'setting-item';
	const requiredLabel = document.createElement('label');
	requiredLabel.textContent = 'Required';
	const requiredToggle = document.createElement('input');
	requiredToggle.type = 'checkbox';
	requiredToggle.checked = question.required || false;
	requiredToggle.addEventListener('change', () => {
		question.required = requiredToggle.checked;
	});
	requiredLabel.insertBefore(requiredToggle, requiredLabel.firstChild);
	requiredDiv.appendChild(requiredLabel);
	
	// Points (for quizzes)
	if (currentSurveyType === 'quiz') {
		const pointsDiv = document.createElement('div');
		pointsDiv.className = 'setting-item';
		const pointsLabel = document.createElement('label');
		pointsLabel.textContent = 'Points:';
		const pointsInput = document.createElement('input');
		pointsInput.type = 'number';
		pointsInput.value = question.points || 1;
		pointsInput.addEventListener('input', () => {
			question.points = parseInt(pointsInput.value) || 1;
			updateSurveyStats();
		});
		pointsDiv.appendChild(pointsLabel);
		pointsDiv.appendChild(pointsInput);
		form.appendChild(pointsDiv);
	}
	
	form.appendChild(requiredDiv);
	
	// Close button
	const closeBtn = document.createElement('button');
	closeBtn.textContent = 'Close';
	closeBtn.className = 'btn primary';
	closeBtn.addEventListener('click', () => {
		modal.remove();
	});
	
	content.appendChild(title);
	content.appendChild(form);
	content.appendChild(closeBtn);
	modal.appendChild(content);
	
	document.body.appendChild(modal);
	
	// Close on backdrop click
	modal.addEventListener('click', (e) => {
		if (e.target === modal) {
			modal.remove();
		}
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
        
        const payload = buildSurveyPayload({
            title: titleEl ? titleEl.value : 'New Survey',
            description: descEl ? descEl.value : '',
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
    const mappedQuestions = questions.map((q, idx) => {
        const base = {
            id: q.id && typeof q.id === 'number' ? q.id : undefined,
            title: q.title || 'Question',
            description: q.description || null,
            type: q.type,
            required: !!q.required,
            display_order: idx,
            metadata: null,
        };
        
        // Add points or weight based on survey type
        if (type === 'quiz') {
            base.points = typeof q.questionPoints === 'number' ? q.questionPoints : 1;
        } else {
            base.weight = typeof q.questionWeight === 'number' ? q.questionWeight : 1;
        }
        
        if (q.type === 'radio' || q.type === 'checkbox' || q.type === 'dropdown') {
            base.options = (q.options || []).map((label, oIdx) => ({
                label,
                weight: type === 'survey' ? (q.weights?.[oIdx] ?? null) : null,
                points: type === 'quiz' ? (q.points?.[oIdx] ?? null) : null,
                is_correct: type === 'quiz' ? (q.correctAnswer === oIdx) : false,
                display_order: oIdx,
            }));
        }
        return base;
    });
    return { title, description, type, is_published, questions: mappedQuestions };
}

// Hook buttons
(() => {
    const publishBtn = document.getElementById('publishBtn');
    if (publishBtn) publishBtn.addEventListener('click', () => saveSurvey({ publish: true }));
    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) previewBtn.addEventListener('click', () => saveSurvey({ publish: false, redirectToPreview: true }));
    // Load existing survey into builder when ?survey=ID is present
    document.addEventListener('DOMContentLoaded', tryLoadSurveyFromQuery);

    // Word import handler
    const importForm = document.getElementById('wordImportForm');
    if (importForm) {
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('wordFile');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) return alert('Choose a .docx or .txt file');
            const fd = new FormData();
            fd.append('file', fileInput.files[0]);
            fd.append('type', document.getElementById('importType')?.value || 'survey');
            try {
                const res = await fetch('/api/surveys/import/word', { method: 'POST', body: fd });
                if (!res.ok) throw new Error('Import failed');
                const json = await res.json();
                const imported = json.questions || [];
                imported.forEach(q => {
                    const id = `q_${Date.now()}_${Math.random().toString(36).slice(2, 6)}`;
                    const mapped = {
                        id,
                        type: q.type || 'short',
                        title: q.title || 'Question',
                        options: Array.isArray(q.options) ? q.options : [],
                        correctAnswer: typeof q.correctAnswer === 'number' ? q.correctAnswer : null,
                        weights: {},
                        points: 1
                    };
                    questions.push(mapped);
                });
                renderQuestions();
                updateSurveyStats();
                alert(`Imported ${imported.length} questions`);
            } catch (err) {
                console.error(err);
                alert('Import error');
            }
        });
    }
})();

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
