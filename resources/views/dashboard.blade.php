<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Analytics Dashboard</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
</head>
<body>
	<div class="app">
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title">AQL Soft</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item active" href="{{ url('/dashboard') }}">Dashboard</a>
				<a class="menu-item" href="{{ url('/') }}">Survey Builder</a>
				<a class="menu-item" href="{{ url('/responses') }}">View Responses</a>
				<!-- <a class="menu-item" href="{{ url('/analytics') }}">Analytics</a>
				<a class="menu-item" href="#">User Management</a> -->
			</nav>
		</aside>

		<main class="content">
			<header class="page-header">
				<h1>Analytics Dashboard</h1>
				<p class="subtitle">Track engagement, performance, and usage across your platform</p>
			</header>

			<section class="stats">
				<div class="card stat"><div class="icon">📄</div><div><div class="num" id="totalSurveys">-</div><div class="lbl">Total Surveys</div></div></div>
				<div class="card stat"><div class="icon">📅</div><div><div class="num" id="totalResponses">-</div><div class="lbl">Total Responses</div></div></div>
				<div class="card stat"><div class="icon">🎯</div><div><div class="num" id="totalQuizzes">-</div><div class="lbl">Total Quizzes</div></div></div>
				<div class="card stat"><div class="icon">📊</div><div><div class="num" id="avgScore">-</div><div class="lbl">Average Score</div></div></div>
			</section>

			<section class="analytics-section" style="margin: 24px 0;">
				<div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 24px;">
					<div class="chart-card" style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
						<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1f2937;">Response Distribution by Survey Type</h3>
						<canvas id="typeChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card" style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
						<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1f2937;">Responses Over Time</h3>
						<canvas id="timeChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card" style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
						<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1f2937;">Top Performing Surveys</h3>
						<canvas id="surveyChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card" style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
						<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1f2937;">Score Distribution</h3>
						<canvas id="scoreChart" width="400" height="200"></canvas>
					</div>
				</div>
			</section>
		</main>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="{{ asset('assets/js/dashboard.js') }}"></script>
	<script>
	// Global variables
	let allSurveys = [];
	let allResponses = [];

	async function init() {
		try {
			console.log('Initializing dashboard...');
			// Load all surveys
			const surveysRes = await fetch('/api/surveys');
			const surveysData = await surveysRes.json();
			allSurveys = surveysData.data || [];

			// Load all responses from all surveys
			await loadAllResponses();

			// Update statistics
			updateStatistics();

			// Create charts
			if (allResponses.length > 0) {
				createCharts();
			}

			console.log('Dashboard initialization completed successfully');

		} catch (error) {
			console.error('Error initializing dashboard:', error);
		}
	}

	async function loadAllResponses() {
		console.log('Loading all responses...');
		allResponses = [];

		if (allSurveys.length === 0) {
			console.warn('No surveys found');
			return;
		}

		// Load all responses from all surveys in parallel
		const responsePromises = allSurveys.map(async (survey) => {
			try {
				const res = await fetch(`/api/surveys/${survey.id}/responses`);
				if (!res.ok) {
					console.warn(`Failed to load responses for survey ${survey.id}: ${res.status}`);
					return [];
				}
				const data = await res.json();
				const responses = data.data?.data || [];

				// Add survey info to each response
				return responses.map(response => ({
					...response,
					survey: survey
				}));
			} catch (error) {
				console.error(`Error loading responses for survey ${survey.id}:`, error);
				return [];
			}
		});

		try {
			const allResponseArrays = await Promise.all(responsePromises);
			allResponses = allResponseArrays.flat();
			console.log(`Loaded ${allResponses.length} responses from ${allSurveys.length} surveys`);
		} catch (error) {
			console.error('Error loading responses:', error);
			allResponses = [];
		}
	}

	function updateStatistics() {
		console.log('Updating statistics...');
		const responses = allResponses;
		const surveys = allSurveys.filter(s => s.type === 'survey').length;
		const quizzes = allSurveys.filter(s => s.type === 'quiz').length;

		const totalScore = responses.reduce((sum, r) => sum + (r.score || 0), 0);
		const avgScore = responses.length > 0 ? Math.round((totalScore / responses.length) * 100) / 100 : 0;

		document.getElementById('totalSurveys').textContent = allSurveys.length;
		document.getElementById('totalResponses').textContent = responses.length;
		document.getElementById('totalQuizzes').textContent = quizzes;
		document.getElementById('avgScore').textContent = avgScore;
	}

	function createCharts() {
		const responses = allResponses;

		if (responses.length === 0) {
			console.warn('No responses data available for charts');
			return;
		}

		// Chart 1: Response Distribution by Survey Type
		const typeCtx = document.getElementById('typeChart').getContext('2d');
		const surveyCount = responses.filter(r => r.survey?.type === 'survey').length;
		const quizCount = responses.filter(r => r.survey?.type === 'quiz').length;

		new Chart(typeCtx, {
			type: 'doughnut',
			data: {
				labels: ['Surveys', 'Quizzes'],
				datasets: [{
					data: [surveyCount, quizCount],
					backgroundColor: ['#3b82f6', '#f59e0b'],
					borderWidth: 0
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: { position: 'bottom' }
				}
			}
		});

		// Chart 2: Responses Over Time
		const timeCtx = document.getElementById('timeChart').getContext('2d');
		const last7Days = [];
		const today = new Date();
		
		for (let i = 6; i >= 0; i--) {
			const date = new Date(today);
			date.setDate(date.getDate() - i);
			const dateStr = date.toISOString().split('T')[0];
			const count = responses.filter(r => {
				const responseDate = new Date(r.created_at).toISOString().split('T')[0];
				return responseDate === dateStr;
			}).length;
			
			last7Days.push({
				date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
				count: count
			});
		}
		
		new Chart(timeCtx, {
			type: 'line',
			data: {
				labels: last7Days.map(d => d.date),
				datasets: [{
					label: 'Responses',
					data: last7Days.map(d => d.count),
					borderColor: '#10b981',
					backgroundColor: 'rgba(16, 185, 129, 0.1)',
					tension: 0.4,
					fill: true
				}]
			},
			options: {
				responsive: true,
				scales: {
					y: { beginAtZero: true }
				}
			}
		});

		// Chart 3: Top Performing Surveys
		const surveyCtx = document.getElementById('surveyChart').getContext('2d');
		const surveyStats = {};

		responses.forEach(r => {
			const surveyTitle = r.survey?.title || 'Unknown';
			if (!surveyStats[surveyTitle]) {
				surveyStats[surveyTitle] = 0;
			}
			surveyStats[surveyTitle]++;
		});

		const topSurveys = Object.entries(surveyStats)
			.sort(([,a], [,b]) => b - a)
			.slice(0, 5);

		if (topSurveys.length > 0) {
			new Chart(surveyCtx, {
				type: 'bar',
				data: {
					labels: topSurveys.map(([title]) => title.length > 20 ? title.substring(0, 20) + '...' : title),
					datasets: [{
						label: 'Responses',
						data: topSurveys.map(([,count]) => count),
						backgroundColor: '#8b5cf6'
					}]
				},
				options: {
					responsive: true,
					scales: {
						y: { beginAtZero: true }
					}
				}
			});
		}

		// Chart 4: Score Distribution
		const scoreCtx = document.getElementById('scoreChart').getContext('2d');
		const scores = responses.map(r => r.score || 0).filter(s => s > 0);

		if (scores.length > 0) {
			const scoreBins = [0, 0, 0, 0, 0]; // 0-20, 21-40, 41-60, 61-80, 81-100

			scores.forEach(score => {
				const bin = Math.min(Math.floor(score / 20), 4);
				scoreBins[bin]++;
			});

			new Chart(scoreCtx, {
				type: 'bar',
				data: {
					labels: ['0-20', '21-40', '41-60', '61-80', '81-100'],
					datasets: [{
						label: 'Count',
						data: scoreBins,
						backgroundColor: '#ef4444'
					}]
				},
				options: {
					responsive: true,
					scales: {
						y: { beginAtZero: true }
					}
				}
			});
		}
	}

	init();
	</script>
</body>
</html>

