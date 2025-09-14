<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Response Detail</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/responses.css') }}">
	<style>
		.app { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
		.content { padding: 24px; background: #f8fafc; }
		.page-header { margin-bottom: 24px; }
		.page-header h1 { margin: 0 0 8px; font-size: 28px; font-weight: 700; color: #1f2937; }
		.subtitle { color: #6b7280; margin: 0; }
		.respondent-info { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 24px; border: 1px solid #e5e7eb; }
		.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
		.info-item { }
		.info-label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
		.info-value { font-size: 16px; font-weight: 500; color: #1f2937; }
		.question-card {
			background: white;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 16px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		
		.question-type {
			background: #e3f2fd;
			color: #1976d2;
			padding: 4px 8px;
			border-radius: 4px;
			font-size: 12px;
			font-weight: 500;
			display: inline-block;
			margin-bottom: 8px;
			text-transform: uppercase;
		}
		
		.question-title {
			font-size: 18px;
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
		}
		
		.question-weight {
			font-size: 14px;
			color: #666;
			margin-bottom: 12px;
			font-style: italic;
		}
		
		.answer-section {
			margin-top: 12px;
		}
		
		.answer-label {
			font-weight: 500;
			color: #666;
			margin-bottom: 4px;
		}
		
		.answer-value {
			color: #333;
			padding: 8px 12px;
			background: #f8f9fa;
			border-radius: 6px;
			border-left: 4px solid #0f62fe;
		}
		
		.answer-comparison {
			display: flex;
			flex-direction: column;
			gap: 12px;
		}
		
		.user-answer, .correct-answer, .optimal-answer {
			padding: 12px;
			border-radius: 6px;
			border: 2px solid;
		}
		
		.user-answer.correct {
			background: #e8f5e8;
			border-color: #4caf50;
		}
		
		.user-answer.incorrect {
			background: #ffebee;
			border-color: #f44336;
		}
		
		.user-answer:not(.correct):not(.incorrect) {
			background: #f8f9fa;
			border-color: #dee2e6;
		}
		
		.correct-answer {
			background: #e8f5e8;
			border-color: #4caf50;
		}
		
		.optimal-answer {
			background: #fff3e0;
			border-color: #ff9800;
		}
		
		.status {
			font-weight: bold;
			margin-left: 8px;
		}
		
		.status.correct {
			color: #4caf50;
		}
		
		.status.incorrect {
			color: #f44336;
		}
		.score-badge { display: inline-block; background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 8px; font-weight: 600; margin-left: 8px; }
		.quiz-score { background: #dbeafe; color: #1e40af; }
		.survey-weight { background: #fef3c7; color: #92400e; }
		.survey-weight.optimal { background: #fff3e0; color: #e65100; }
		.total-score { background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-top: 24px; text-align: center; }
		.total-label { font-size: 14px; color: #6b7280; margin-bottom: 8px; }
		.total-value { font-size: 32px; font-weight: 700; color: #1f2937; }
		.back-btn { display: inline-flex; align-items: center; gap: 8px; background: #f3f4f6; color: #374151; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; margin-bottom: 16px; }
		.back-btn:hover { background: #e5e7eb; }
	</style>
</head>
<body>
	<div class="app">
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title">AQL Soft</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item active" href="{{ url('/responses') }}">👁️ <span>View Responses</span></a>
				<!-- <a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a> -->
				<!-- <a class="menu-item" href="#">👥 <span>User Management</span></a> -->
			</nav>
		</aside>
		<main class="content">
			<a href="/responses" class="back-btn">← Back to Responses</a>
			
			<header class="page-header">
				<h1 id="pageTitle">Response Details</h1>
				<p class="subtitle" id="pageSubtitle">Loading...</p>
			</header>

			<div id="respondentInfo" class="respondent-info" style="display:none;">
				<div class="info-grid">
					<div class="info-item">
						<div class="info-label">Name</div>
						<div class="info-value" id="respondentName">-</div>
					</div>
					<div class="info-item">
						<div class="info-label">Email</div>
						<div class="info-value" id="respondentEmail">-</div>
					</div>
					<div class="info-item">
						<div class="info-label">Survey Number</div>
						<div class="info-value" id="surveyNumber">-</div>
					</div>
					<div class="info-item">
						<div class="info-label">Response Date</div>
						<div class="info-value" id="responseDate">-</div>
					</div>
				</div>
			</div>

			<div id="questionsContainer"></div>
			
			<div id="totalScore" class="total-score" style="display:none;">
				<div class="total-label" id="scoreLabel">Total Score</div>
				<div class="total-value" id="scoreValue">0</div>
			</div>

			<div id="errorBox" style="display:none; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; padding:12px; border-radius:10px; margin-top:12px;"></div>
		</main>
	</div>

	<script>
	// Get survey ID and respondent ID from URL
	const params = new URLSearchParams(location.search);
	const surveyId = {{ $surveyId ?? 'null' }};
	const respondentId = params.get('respondent');

	async function loadResponseDetails() {
		try {
			if (!surveyId || !respondentId) {
				throw new Error('Missing survey ID or respondent ID');
			}

			// Load survey info
			const surveyRes = await fetch(`/api/surveys/${surveyId}`);
			const surveyData = await surveyRes.json();
			const survey = surveyData;

			// Load responses for this survey and respondent
			const responsesRes = await fetch(`/api/surveys/${surveyId}/responses?respondent=${respondentId}`);
			const responsesData = await responsesRes.json();
			const responses = responsesData.data?.data || responsesData.data || [];

			// Find the specific respondent's responses
			const respondentResponses = responses.filter(r => r.respondent?.id == respondentId);
			if (respondentResponses.length === 0) {
				throw new Error('No responses found for this respondent');
			}

			const respondent = respondentResponses[0].respondent;
			const allAnswers = respondentResponses.flatMap(r => r.answers || []);

			// Update page header
			document.getElementById('pageTitle').textContent = `${respondent.name}'s Response`;
			document.getElementById('pageSubtitle').textContent = `${survey.title} (${survey.type === 'quiz' ? 'Quiz' : 'Survey'})`;

			// Update respondent info
			document.getElementById('respondentName').textContent = respondent.name || '-';
			document.getElementById('respondentEmail').textContent = respondent.email || '-';
			document.getElementById('surveyNumber').textContent = survey.survey_number || '-';
			document.getElementById('responseDate').textContent = new Date(respondentResponses[0].created_at).toLocaleDateString();
			document.getElementById('respondentInfo').style.display = 'block';

			// Use questions from survey data (already loaded with survey)
			const questions = survey.questions || [];

			const container = document.getElementById('questionsContainer');
			container.innerHTML = '';

			let totalScore = 0;
			let totalWeight = 0;

			questions.forEach(question => {
				const answer = allAnswers.find(a => a.question_id === question.id);
				
				const questionCard = document.createElement('div');
				questionCard.className = 'question-card';
				
				let userAnswerDisplay = '';
				let correctAnswerDisplay = '';
				let scoreDisplay = '';
				let comparisonDisplay = '';
				
				// Find correct answer for this question
				let correctOption = null;
				if (question.options && question.options.length > 0) {
					correctOption = question.options.find(opt => opt.is_correct);
				}
				
				if (answer) {
					if (answer.value) {
						userAnswerDisplay = answer.value;
					} else if (answer.option) {
						userAnswerDisplay = answer.option.label || answer.option.text;
						
						// Calculate score based on survey type
						if (survey.type === 'quiz') {
							if (answer.option.points !== undefined) {
								totalScore += answer.option.points || 0;
								scoreDisplay = `<span class="score-badge quiz-score">${answer.option.points || 0} points</span>`;
							}
							
							// Show if answer is correct or incorrect
							if (correctOption) {
								const isCorrect = answer.option.id === correctOption.id;
								comparisonDisplay = `
									<div class="answer-comparison">
										<div class="user-answer ${isCorrect ? 'correct' : 'incorrect'}">
											<strong>Your Answer:</strong> ${userAnswerDisplay} 
											${isCorrect ? '<span class="status correct">✓ Correct</span>' : '<span class="status incorrect">✗ Incorrect</span>'}
										</div>
										<div class="correct-answer">
											<strong>Correct Answer:</strong> ${correctOption.label || correctOption.text}
											<span class="score-badge quiz-score">${correctOption.points || 0} points</span>
										</div>
									</div>
								`;
							}
						} else if (survey.type === 'survey') {
							if (answer.option.weight !== undefined) {
								totalWeight += answer.option.weight || 0;
								scoreDisplay = `<span class="score-badge survey-weight">${answer.option.weight || 0} weight</span>`;
							}
							
							// Show weight comparison for surveys
							if (correctOption && correctOption.weight !== undefined) {
								comparisonDisplay = `
									<div class="answer-comparison">
										<div class="user-answer">
											<strong>Your Answer:</strong> ${userAnswerDisplay}
											<span class="score-badge survey-weight">${answer.option.weight || 0} weight</span>
										</div>
										<div class="optimal-answer">
											<strong>Highest Weight Option:</strong> ${correctOption.label || correctOption.text}
											<span class="score-badge survey-weight optimal">${correctOption.weight || 0} weight</span>
										</div>
									</div>
								`;
							}
						}
					}
				} else {
					userAnswerDisplay = 'No answer provided';
					if (correctOption) {
						if (survey.type === 'quiz') {
							comparisonDisplay = `
								<div class="answer-comparison">
									<div class="user-answer incorrect">
										<strong>Your Answer:</strong> No answer provided
										<span class="status incorrect">✗ Incorrect</span>
									</div>
									<div class="correct-answer">
										<strong>Correct Answer:</strong> ${correctOption.label || correctOption.text}
										<span class="score-badge quiz-score">${correctOption.points || 0} points</span>
									</div>
								</div>
							`;
						} else if (survey.type === 'survey') {
							comparisonDisplay = `
								<div class="answer-comparison">
									<div class="user-answer">
										<strong>Your Answer:</strong> No answer provided
									</div>
									<div class="optimal-answer">
										<strong>Highest Weight Option:</strong> ${correctOption.label || correctOption.text}
										<span class="score-badge survey-weight optimal">${correctOption.weight || 0} weight</span>
									</div>
								</div>
							`;
						}
					}
				}

				questionCard.innerHTML = `
					<div class="question-type">${question.type}</div>
					<div class="question-title">${question.title}</div>
					<div class="question-weight">Question Weight: ${question.weight || 1.0}</div>
					<div class="answer-section">
						${comparisonDisplay || `
							<div class="answer-label">Answer:</div>
							<div class="answer-value">${userAnswerDisplay} ${scoreDisplay}</div>
						`}
					</div>
				`;
				
				container.appendChild(questionCard);
			});

			// Show total score/weight
			const scoreContainer = document.getElementById('totalScore');
			const scoreLabel = document.getElementById('scoreLabel');
			const scoreValue = document.getElementById('scoreValue');
			
			if (survey.type === 'quiz') {
				scoreLabel.textContent = 'Total Points';
				scoreValue.textContent = totalScore;
				scoreContainer.style.display = 'block';
			} else if (survey.type === 'survey') {
				scoreLabel.textContent = 'Total Weight';
				scoreValue.textContent = totalWeight;
				scoreContainer.style.display = 'block';
			}

		} catch (error) {
			document.getElementById('errorBox').style.display = 'block';
			document.getElementById('errorBox').textContent = 'Error loading response details: ' + error.message;
		}
	}

	loadResponseDetails();
	</script>
</body>
</html>


