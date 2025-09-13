<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>All Survey Responses</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/responses.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/analytics.css') }}">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		.analytics-section {
			margin: 24px 0;
		}
		.analytics-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
			gap: 20px;
			margin-bottom: 24px;
		}
		.chart-card {
			background: #fff;
			border-radius: 12px;
			padding: 20px;
			border: 1px solid #e5e7eb;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		}
		.chart-card h3 {
			margin: 0 0 16px 0;
			font-size: 16px;
			font-weight: 600;
			color: #1f2937;
		}
		.chart-card canvas {
			max-height: 200px;
		}
		.icon-btn {
			background: #f3f4f6;
			border: none;
			border-radius: 6px;
			padding: 6px 8px;
			cursor: pointer;
			font-size: 14px;
		}
		.icon-btn:hover {
			background: #e5e7eb;
		}
	</style>
</head>
<body>
	<div class="app">
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title">AQl Soft</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item active" href="{{ url('/responses') }}">👁️ <span>View Responses</span></a>
				<!-- <a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a> -->
			</nav>
		</aside>

		<main class="content">
			<header class="page-header">
				<h1>All Survey Responses</h1>
				<p class="subtitle">View and analyze all responses from surveys and quizzes</p>
		<div class="actions">
					<button class="btn" id="exportBtn" disabled>⬇️ Export All</button>
				</div>
			</header>

			<section class="toolbar">
				<div class="search">
					<input type="search" id="searchInput" placeholder="Search by name, email, or survey..." />
				</div>
			</section>

			<section class="stats" id="stats">
				<div class="stat"><div class="num" id="totalResponses">0</div><div class="lbl">Total Responses</div></div>
				<div class="stat"><div class="num" id="totalSurveys">0</div><div class="lbl">Total Surveys</div></div>
				<div class="stat"><div class="num" id="totalQuizzes">0</div><div class="lbl">Total Quizzes</div></div>
				<div class="stat"><div class="num" id="avgScore">0</div><div class="lbl">Average Score</div></div>
			</section>

			<section class="analytics-section">
				<div class="analytics-grid">
					<div class="chart-card">
						<h3>Response Distribution by Survey Type</h3>
						<canvas id="typeChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card">
						<h3>Responses Over Time</h3>
						<canvas id="timeChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card">
						<h3>Top Performing Surveys</h3>
						<canvas id="surveyChart" width="400" height="200"></canvas>
					</div>
					<div class="chart-card">
						<h3>Score Distribution</h3>
						<canvas id="scoreChart" width="400" height="200"></canvas>
					</div>
				</div>
			</section>

			<section class="data-table-section">
				<div class="table-header">
					<h3>All Survey Respondents</h3>
					<div class="table-actions">
						<button class="btn" id="exportPeopleData">📥 Export Data</button>
						<button class="btn" id="refreshData">🔄 Refresh</button>
					</div>
				</div>
				
				<div class="table-container">
					<div id="loadingIndicator" style="text-align: center; padding: 40px; color: #6b7280;">
						Loading responses...
					</div>
					<table id="peopleTable" style="display: none;">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Survey Title</th>
								<th>Survey Number</th>
								<th>Type</th>
								<th> Weight/ًScore</th>
								<th>Date</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody id="peopleTbody">
							<!-- Data will be populated by JavaScript -->
						</tbody>
					</table>
				</div>
			</section>

		</main>
	</div>

	<script>
	// Global variables
	let allSurveys = [];
	let allResponses = [];
	let filteredResponses = [];

	async function init() {
		try {
			console.log('Initializing responses page...');
			// Load all surveys
			console.log('Fetching surveys...');
			const surveysRes = await fetch('/api/surveys');
			console.log('Surveys response status:', surveysRes.status);
			const surveysData = await surveysRes.json();
			console.log('Surveys data:', surveysData);
			allSurveys = surveysData.data || [];
			console.log(`Loaded ${allSurveys.length} surveys`);

			// Load all responses from all surveys
			await loadAllResponses();

			// Update statistics
			updateStatistics();

			// Create charts only if we have data
			if (allResponses.length > 0) {
				createCharts();
			}

			// Populate table
			populateTable();

			// Hide loading and show table
			document.getElementById('loadingIndicator').style.display = 'none';
			document.getElementById('peopleTable').style.display = 'table';

			// Enable export button
			const exportBtn = document.getElementById('exportBtn');
			if (exportBtn) exportBtn.disabled = false;

			// Setup search
			setupSearch();

			console.log('Page initialization completed successfully');
			console.log('Final state:', { allSurveys: allSurveys.length, allResponses: allResponses.length, filteredResponses: filteredResponses.length });

		} catch (error) {
			console.error('Error initializing:', error);
			// Show error message to user
			document.querySelector('.subtitle').textContent = 'Error loading data. Please refresh the page.';
			document.getElementById('loadingIndicator').innerHTML = `
				<div style="color: #ef4444; margin-bottom: 16px;">Error loading data. Please try again.</div>
				<button class="btn" onclick="location.reload()" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">🔄 Refresh Page</button>
			`;
		}
	}

	async function loadAllResponses() {
		console.log('Loading all responses...');
		allResponses = [];

		if (allSurveys.length === 0) {
			console.warn('No surveys found');
			filteredResponses = [];
			return;
		}

		console.log(`Loading responses for ${allSurveys.length} surveys`);

		// Load all responses from all surveys in parallel for better performance
		const responsePromises = allSurveys.map(async (survey) => {
			try {
				console.log(`Fetching responses for survey ${survey.id}...`);
				const res = await fetch(`/api/surveys/${survey.id}/responses`);
				console.log(`Response status for survey ${survey.id}:`, res.status);
				if (!res.ok) {
					console.warn(`Failed to load responses for survey ${survey.id}: ${res.status}`);
					return [];
				}
				const data = await res.json();
				console.log(`Data for survey ${survey.id}:`, data);
				const responses = data.data?.data || [];
				console.log(`Found ${responses.length} responses for survey ${survey.id}`);

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
			filteredResponses = [...allResponses];
			console.log(`Loaded ${allResponses.length} responses from ${allSurveys.length} surveys`);
			console.log('Sample response:', allResponses[0]);
			console.log('All responses:', allResponses);
		} catch (error) {
			console.error('Error loading responses:', error);
			allResponses = [];
			filteredResponses = [];
		}
	}

	function updateStatistics() {
		console.log('Updating statistics...');
		const responses = allResponses;
		const surveys = allSurveys.filter(s => s.type === 'survey').length;
		const quizzes = allSurveys.filter(s => s.type === 'quiz').length;

		const totalScore = responses.reduce((sum, r) => sum + (r.score || 0), 0);
		const avgScore = responses.length > 0 ? Math.round((totalScore / responses.length) * 100) / 100 : 0;

		console.log(`Statistics: ${responses.length} responses, ${surveys} surveys, ${quizzes} quizzes, avg score: ${avgScore}`);

		document.getElementById('totalResponses').textContent = responses.length;
		document.getElementById('totalSurveys').textContent = surveys;
		document.getElementById('totalQuizzes').textContent = quizzes;
		document.getElementById('avgScore').textContent = avgScore;
	}

	function createCharts() {
		const responses = allResponses;

		if (responses.length === 0) {
			console.warn('No responses data available for charts');
			// Show message in chart containers
			document.querySelectorAll('.chart-card').forEach(card => {
				const canvas = card.querySelector('canvas');
				if (canvas) {
					const ctx = canvas.getContext('2d');
					ctx.font = '16px Arial';
					ctx.fillStyle = '#6b7280';
					ctx.textAlign = 'center';
					ctx.fillText('No data available', canvas.width / 2, canvas.height / 2);
				}
			});
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
		} else {
			const ctx = surveyCtx;
			ctx.font = '16px Arial';
			ctx.fillStyle = '#6b7280';
			ctx.textAlign = 'center';
			ctx.fillText('No survey data', ctx.canvas.width / 2, ctx.canvas.height / 2);
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
		} else {
			const ctx = scoreCtx;
			ctx.font = '16px Arial';
			ctx.fillStyle = '#6b7280';
			ctx.textAlign = 'center';
			ctx.fillText('No score data', ctx.canvas.width / 2, ctx.canvas.height / 2);
		}
	}

	function populateTable() {
		console.log('Populating table with', filteredResponses.length, 'responses');
		const tbody = document.getElementById('peopleTbody');
		tbody.innerHTML = '';

		if (filteredResponses.length === 0) {
			const tr = document.createElement('tr');
			tr.innerHTML = `
				<td colspan="9" style="text-align: center; padding: 40px; color: #6b7280;">
					${allResponses.length === 0 ? 'No responses found' : 'No responses match your search criteria'}
				</td>
			`;
			tbody.appendChild(tr);
			return;
		}

		filteredResponses.forEach((response, index) => {
			const respondent = response.respondent;
			if (!respondent) return; // Skip anonymous responses

			console.log(`Processing response ${index + 1}:`, response);

			const tr = document.createElement('tr');
			tr.innerHTML = `
				<td>${index + 1}</td>
				<td>${respondent.name || '-'}</td>
				<td>${respondent.email || '-'}</td>
				<td>${response.survey?.title || 'Unknown Survey'}</td>
				<td>${response.survey?.survey_number || 'N/A'}</td>
				<td>${response.survey?.type === 'quiz' ? 'Quiz' : 'Survey'}</td>
				<td>${response.score || 0}</td>
				<td>${new Date(response.created_at).toLocaleDateString()}</td>
				<td>
					<button class="icon-btn" onclick="viewResponse(${response.survey?.id}, ${respondent.id})" title="View Details">👁️</button>
				</td>
			`;
			tbody.appendChild(tr);
		});

		console.log(`Table populated with ${filteredResponses.length} rows`);
	}
	

	function setupSearch() {
		const searchInput = document.getElementById('searchInput');
		if (!searchInput) return;

		searchInput.addEventListener('input', (e) => {
			const query = e.target.value.toLowerCase();

			if (!query) {
				filteredResponses = [...allResponses];
			} else {
				filteredResponses = allResponses.filter(response => {
					const respondent = response.respondent;
					const survey = response.survey;

					return (
						(respondent?.name?.toLowerCase().includes(query)) ||
						(respondent?.email?.toLowerCase().includes(query)) ||
						(survey?.title?.toLowerCase().includes(query)) ||
						(survey?.type?.toLowerCase().includes(query))
					);
				});
			}

			populateTable();
		});
	}

	// Export all data
	document.getElementById('exportBtn')?.addEventListener('click', () => {
		exportAllData();
	});

	// Export people data
	document.getElementById('exportPeopleData')?.addEventListener('click', () => {
		exportAllData();
	});

	// Refresh data
	document.getElementById('refreshData')?.addEventListener('click', () => {
		init();
	});

	// View response details function
	window.viewResponse = function(surveyId, respondentId) {
		if (!surveyId || !respondentId) {
			console.error('Invalid survey or respondent ID');
			alert('Unable to view response details. Missing required information.');
			return;
		}
		window.location.href = `/response-detail/${surveyId}?respondent=${respondentId}`;
	};

	// Export all data to CSV
	function exportAllData() {
		if (filteredResponses.length === 0) {
			alert('No data available to export');
			return;
		}

		const data = filteredResponses.map((response, index) => {
			const respondent = response.respondent;
			return {
				number: index + 1,
				name: respondent?.name || '-',
				email: respondent?.email || '-',
				surveyTitle: response.survey?.title || 'Unknown Survey',
				surveyNumber: response.survey?.survey_number || 'N/A',
				type: response.survey?.type === 'quiz' ? 'Quiz' : 'Survey',
				score: response.score || 0,
				date: new Date(response.created_at).toLocaleDateString()
			};
		});

		const headers = ['#', 'Name', 'Email', 'Survey Title', 'Survey Number', 'Type', 'Score', 'Date'];
		const csvContent = [
			headers.join(','),
			...data.map(d => [
				d.number, d.name, d.email, d.surveyTitle, d.surveyNumber, d.type, d.score, d.date
			].join(','))
		].join('\n');

		const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');
		const url = URL.createObjectURL(blob);
		link.setAttribute('href', url);
		link.setAttribute('download', 'all_survey_responses.csv');
		link.style.visibility = 'hidden';
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
	}

	init();
	</script>
</body>
</html>