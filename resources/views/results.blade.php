<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Survey Results</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: 'Inter', 'Cairo', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
		.results-container { background: white; border-radius: 20px; padding: 40px; max-width: 800px; width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
		.results-header { text-align: center; margin-bottom: 30px; }
		.results-title { font-size: 32px; font-weight: 700; color: #1f2937; margin-bottom: 10px; }
		.results-subtitle { color: #6b7280; font-size: 16px; }
		.score-card { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 16px; padding: 30px; text-align: center; margin-bottom: 30px; }
		.score-card.failed { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
		.score-value { font-size: 48px; font-weight: 700; margin-bottom: 10px; }
		.score-label { font-size: 18px; opacity: 0.9; }
		.status-message { background: #f0fdf4; border: 2px solid #bbf7d0; color: #166534; padding: 20px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-weight: 600; }
		.status-message.failed { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
		.question-results { margin-bottom: 30px; }
		.question-card { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
		.question-title { font-weight: 600; color: #1f2937; margin-bottom: 12px; }
		.answer-comparison { display: flex; flex-direction: column; gap: 12px; }
		.user-answer, .correct-answer { padding: 12px; border-radius: 8px; border: 2px solid; }
		.user-answer.correct { background: #dcfce7; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1); }
		.user-answer.incorrect { background: #fef2f2; border-color: #dc2626; }
		.user-answer.neutral { background: #f1f5f9; border-color: #64748b; }
		.correct-answer { background: #dcfce7; border-color: #16a34a; }
		.optimal-answer { background: #fff3e0; border-color: #f59e0b; }
		.status-icon { font-weight: bold; margin-left: 8px; }
		.status-icon.correct { color: #16a34a; }
		.status-icon.incorrect { color: #dc2626; }
		.actions { display: flex; gap: 16px; justify-content: center; margin-top: 30px; }
		.btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
		.btn-primary { background: #3b82f6; color: white; }
		.btn-secondary { background: #f1f5f9; color: #475569; }
		.btn:hover { transform: translateY(-2px); transition: all 0.2s; }
		.account-info { background: #eff6ff; border: 2px solid #bfdbfe; color: #1e40af; padding: 20px; border-radius: 12px; margin-top: 20px; }
		.account-credentials { background: #f8fafc; padding: 16px; border-radius: 8px; margin-top: 12px; font-family: monospace; }
		.survey-thank-you { text-align: center; margin-bottom: 30px; }
		.thank-you-card { background: #f0f9ff; border: 2px solid #0ea5e9; color: #0c4a6e; padding: 30px; border-radius: 16px; }
		.thank-you-card h2 { margin-bottom: 15px; font-size: 24px; }
		.thank-you-card p { font-size: 16px; line-height: 1.6; }
	</style>
</head>
<body>
	<div class="results-container">
		<div class="results-header">
			<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
				<button onclick="history.back()" class="btn" style="background: #6b7280; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">← Back</button>
				<h1 class="results-title" id="surveyTitle">Survey Results</h1>
			</div>
			<p class="results-subtitle" id="surveySubtitle">Thank you for completing the survey</p>
		</div>

		<div class="score-card" id="scoreCard" style="display: none;">
			<div class="score-value" id="scoreValue">0</div>
			<div class="score-label" id="scoreLabel">Total Score</div>
		</div>

		<div class="status-message" id="statusMessage">
			Calculating your results...
		</div>

		<div class="survey-thank-you" id="surveyThankYou" style="display: none;">
			<div class="thank-you-card">
				<h2>🙏 شكراً لك على المشاركة</h2>
				<p>تم تسجيل إجاباتك بنجاح. في حال تم قبول طلبك، سيتم التواصل معك قريباً.</p>
			</div>
		</div>

		<div class="question-results" id="questionResults">
			<!-- Questions will be populated here -->
		</div>

		<div class="account-info" id="accountInfo" style="display: none;">
			<h3>🎉 تهانينا! تم إنشاء حسابك بنجاح</h3>
			<p>لقد نجحت في اجتياز الاختبار. بيانات دخولك للأكاديمية:</p>
			<div class="account-credentials">
				<strong>الإيميل:</strong> <span id="accountEmail"></span><br>
				<strong>كلمة المرور:</strong> <span id="accountPassword"></span><br>
				<strong>رابط الأكاديمية:</strong> <span id="academyUrl"></span>
			</div>
			<p style="margin-top: 12px; font-size: 14px;">احفظ هذه البيانات للدخول إلى الموقع الرسمي للأكاديمية.</p>
		</div>

		<div class="actions">
			<button class="btn btn-secondary" id="downloadBtn">📥 تحميل الأجوبة</button>
			<a href="#" class="btn btn-primary" id="academyPortalBtn" style="display: none;">دخول الأكاديمية</a>
		</div>
	</div>

	<script>
	// Get survey ID and response data from URL parameters
	const params = new URLSearchParams(location.search);
	const surveyId = params.get('survey');
	const responseId = params.get('response');

	async function loadResults() {
		try {
			if (!surveyId || !responseId) {
				throw new Error('Missing survey or response ID');
			}

			// Load survey info
			const surveyRes = await fetch(`/api/surveys/${surveyId}`);
			if (!surveyRes.ok) {
				throw new Error(`Failed to load survey: ${surveyRes.status}`);
			}
			const surveyData = await surveyRes.json();
			const survey = surveyData;

			// Load response details
			const responseRes = await fetch(`/api/responses/${responseId}`);
			if (!responseRes.ok) {
				throw new Error(`Failed to load response: ${responseRes.status}`);
			}
			const responseData = await responseRes.json();
			const response = responseData.data;

			// Update page header
			document.getElementById('surveyTitle').textContent = survey.title;
			document.getElementById('surveySubtitle').textContent = `${survey.type === 'quiz' ? 'Quiz' : 'Survey'} Results`;

			// Calculate total score
			let totalScore = 0;
			let maxScore = 0;
			let correctAnswers = 0;
			let totalQuestions = 0;

			const questionsContainer = document.getElementById('questionResults');
			questionsContainer.innerHTML = '';

			// Use questions from survey data (already loaded with survey)
			const questions = survey.questions || [];

			questions.forEach(question => {
				const answer = response.answers.find(a => a.question_id === question.id);
				totalQuestions++;

				const questionCard = document.createElement('div');
				questionCard.className = 'question-card';

				let userAnswerDisplay = '';
				let correctAnswerDisplay = '';
				let isCorrect = false;
				let questionScore = 0;
				let maxQuestionScore = 0;

				// Handle different question types
				if (question.type === 'short' || question.type === 'long' || question.type === 'date' || question.type === 'number') {
					// Text questions - user gets full weight if answered
					if (answer && answer.value && answer.value.trim() !== '') {
						questionScore = question.weight || 1;
						isCorrect = true;
					}
					maxQuestionScore = question.weight || 1;
					userAnswerDisplay = answer ? (answer.value || 'No answer') : 'No answer';
					correctAnswerDisplay = 'Any valid answer';
				} else if (question.options && question.options.length > 0) {
					// Multiple choice questions
					if (answer && answer.option) {
						userAnswerDisplay = answer.option.label || answer.option.text || 'غير محدد';
						
						if (survey.type === 'quiz') {
							// Quiz mode - check if correct
							const correctOption = question.options.find(opt => opt.is_correct);
							if (correctOption) {
								isCorrect = answer.option.id === correctOption.id;
								questionScore = isCorrect ? (question.points || 1) : 0;
								maxQuestionScore = question.points || 1;
								correctAnswerDisplay = correctOption.label || correctOption.text || 'غير محدد';
							}
						} else {
							// Survey mode - use weights
							questionScore = answer.option.weight || 0;
							const maxWeightOption = question.options.reduce((max, opt) => 
								(opt.weight || 0) > (max.weight || 0) ? opt : max, question.options[0]);
							maxQuestionScore = maxWeightOption.weight || 0;
							correctAnswerDisplay = `Best option: ${maxWeightOption.label || maxWeightOption.text || 'غير محدد'}`;
							isCorrect = answer.option.id === maxWeightOption.id;
						}
					} else {
						userAnswerDisplay = 'No answer';
						if (survey.type === 'quiz') {
							const correctOption = question.options.find(opt => opt.is_correct);
							correctAnswerDisplay = correctOption ? (correctOption.label || correctOption.text || 'غير محدد') : 'No correct answer set';
							maxQuestionScore = question.points || 1;
						} else {
							const maxWeightOption = question.options.reduce((max, opt) => 
								(opt.weight || 0) > (max.weight || 0) ? opt : max, question.options[0]);
							correctAnswerDisplay = `Best option: ${maxWeightOption.label || maxWeightOption.text || 'غير محدد'}`;
							maxQuestionScore = maxWeightOption.weight || 0;
						}
					}
				}

				totalScore += questionScore;
				maxScore += maxQuestionScore;
				if (isCorrect) correctAnswers++;

				// Create question display
				let answerHtml = `
					<div class="user-answer ${isCorrect ? 'correct' : (answer ? 'incorrect' : 'neutral')}">
						<strong>إجابتك:</strong> ${userAnswerDisplay}
						${isCorrect ? '<span class="status-icon correct">✓</span>' : (answer ? '<span class="status-icon incorrect">✗</span>' : '')}
					</div>
				`;

				// Display answers comparison
				if (survey.type === 'quiz') {
					// For quizzes, show correct answer only if user answer is incorrect
					const correctOption = question.options.find(opt => opt.is_correct);
					if (correctOption && !isCorrect) {
						answerHtml += `
							<div class="correct-answer">
								<strong>✅ الإجابة الصحيحة:</strong> ${correctOption.label || correctOption.text || 'غير محدد'}
							</div>
						`;
					}
				} else {
					// For surveys, show optimal answer only if user didn't choose it
					const optimalOption = question.options.reduce((max, opt) => 
						(opt.weight || 0) > (max.weight || 0) ? opt : max, question.options[0]);
					
					if (optimalOption && optimalOption.weight > 0 && !isCorrect) {
						answerHtml += `
							<div class="optimal-answer">
								<strong>⭐ الإجابة المثلى:</strong> ${optimalOption.label || optimalOption.text || 'غير محدد'}
							</div>
						`;
					}
				}

				questionCard.innerHTML = `
					<div class="question-title">${question.title}</div>
					<div class="answer-comparison">
						${answerHtml}
					</div>
				`;

				questionsContainer.appendChild(questionCard);
			});

			// Update score display
			const scoreCard = document.getElementById('scoreCard');
			const scoreValue = document.getElementById('scoreValue');
			const scoreLabel = document.getElementById('scoreLabel');
			const statusMessage = document.getElementById('statusMessage');
			const surveyThankYou = document.getElementById('surveyThankYou');

			if (survey.type === 'quiz') {
				const percentage = maxScore > 0 ? Math.round((totalScore / maxScore) * 100) : 0;
				scoreValue.textContent = `${percentage}%`;
				scoreLabel.textContent = `${totalScore}/${maxScore} Points`;
				
				if (percentage >= 90) {
					statusMessage.textContent = '🎉 Excellent! You passed with flying colors!';
					statusMessage.className = 'status-message';
					await createUserAccount(response.respondent, percentage);
				} else if (percentage >= 70) {
					statusMessage.textContent = '👍 Good job! You passed the assessment.';
					statusMessage.className = 'status-message';
				} else {
					statusMessage.textContent = '📚 Keep studying! You can retake the quiz.';
					statusMessage.className = 'status-message failed';
					scoreCard.className = 'score-card failed';
				}
				scoreCard.style.display = 'block';
				// Show detailed question results for quiz
				questionsContainer.style.display = 'block';
			} else {
				// For surveys, only show thank you message
				surveyThankYou.style.display = 'block';
				// Hide detailed question results for surveys
				questionsContainer.style.display = 'none';
				statusMessage.style.display = 'none';
			}

			// Setup download functionality
			setupDownloadButton(survey, response);

		} catch (error) {
			console.error('Error loading results:', error);
			document.getElementById('statusMessage').textContent = 'Error loading results: ' + error.message;
			document.getElementById('statusMessage').className = 'status-message failed';
		}
	}

	function setupDownloadButton(survey, response) {
		const downloadBtn = document.getElementById('downloadBtn');
		downloadBtn.addEventListener('click', function() {
			downloadAnswers(survey, response);
		});
	}

	function downloadAnswers(survey, response) {
		let content = `${survey.title}\n`;
		content += `نوع الاستبيان: ${survey.type === 'quiz' ? 'كويز' : 'استطلاع'}\n`;
		content += `التاريخ: ${new Date().toLocaleDateString('ar-SA')}\n`;
		content += `المشارك: ${response.respondent.name || 'غير محدد'}\n`;
		content += `البريد الإلكتروني: ${response.respondent.email}\n\n`;
		content += '='.repeat(50) + '\n\n';

		survey.questions.forEach((question, index) => {
			const answer = response.answers.find(a => a.question_id === question.id);
			content += `${index + 1}. ${question.title}\n`;
			
			if (answer) {
				if (answer.value) {
					content += `الإجابة: ${answer.value}\n`;
				} else if (answer.option) {
					content += `الإجابة: ${answer.option.label || answer.option.text}\n`;
				}
			} else {
				content += `الإجابة: لم يتم الإجابة\n`;
			}
			content += '\n';
		});

		// Create and download file
		const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
		const url = window.URL.createObjectURL(blob);
		const a = document.createElement('a');
		a.href = url;
		a.download = `${survey.title}_answers_${new Date().toISOString().split('T')[0]}.txt`;
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		window.URL.revokeObjectURL(url);
	}

	async function createUserAccount(respondent, score) {
		try {
			const response = await fetch('/api/create-account', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					name: respondent.name,
					email: respondent.email,
					score: score
				})
			});

			if (response.ok) {
				const data = await response.json();
				document.getElementById('accountInfo').style.display = 'block';
				document.getElementById('accountEmail').textContent = data.email;
				document.getElementById('accountPassword').textContent = data.password;
				document.getElementById('academyUrl').textContent = data.academy_url;
				document.getElementById('academyPortalBtn').href = data.academy_url;
				document.getElementById('academyPortalBtn').style.display = 'inline-flex';
			}
		} catch (error) {
			console.error('Error creating account:', error);
		}
	}

	// Load results when page loads
	loadResults();
	</script>
</body>
</html>
