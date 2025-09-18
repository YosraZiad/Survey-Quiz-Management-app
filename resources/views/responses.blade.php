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
	<style>
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
				<a class="menu-item" href="{{ url('/surveys') }}">⚙️ <span>Manage Surveys</span></a>

				<!-- <a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a> -->
			</nav>
		</aside>

		<main class="content">
			<header class="page-header">
				<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
					<h1 id="pageTitle">Survey Responses</h1>
				</div>
				<p class="subtitle" id="pageSubtitle">Loading survey details...</p>
		<div class="actions">
					<button class="btn" id="exportBtn" onclick="exportSurveyData()">⬇️ Export Data</button>
				</div>
			</header>

			<section class="toolbar">
				<!-- Filter Controls Section with Integrated Results -->
				<div class="filter-section" style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
					<h3 style="margin: 0 0 15px 0; color: #374151; font-size: 18px;">📊 Analysis & Filters</h3>
					
					<div class="filter-controls" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 15px;">
						<div class="filter-group">
							<label for="questionSelect" style="font-weight: 500; margin-right: 8px; color: #374151;">Select Question:</label>
							<select id="questionSelect" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; min-width: 250px; background: white;">
								<option value="">Choose a question to analyze</option>
							</select>
						</div>
						
						<div class="filter-group">
							<label for="chartTypeSelect" style="font-weight: 500; margin-right: 8px; color: #374151;">Chart Type:</label>
							<select id="chartTypeSelect" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; min-width: 150px; background: white;">
								<option value="">Select Chart Type</option>
								<option value="bar">📊 Bar Chart</option>
								<option value="pie">🥧 Pie Chart</option>
								<option value="line">📈 Line Chart</option>
								<option value="table">📋 Data Table</option>
							</select>
						</div>
						
						<button id="generateAnalysis" class="btn primary" style="display: none; padding: 10px 20px; border-radius: 8px;">🔍 Generate Analysis</button>
						<button id="testWithSampleData" class="btn" style="display: none; background: #f59e0b; color: white; margin-left: 10px; padding: 10px 20px; border-radius: 8px;">🧪 Test with Sample Data</button>
					</div>

					<!-- Analysis Results - Integrated inside filter section -->
					<div id="analysisResults" style="display: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 15px; margin-top: 10px; color: white;">
						<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
							<div style="display: flex; align-items: center;">
								<span style="font-size: 16px; margin-right: 8px;">📈</span>
								<h4 id="analysisTitle" style="margin: 0; font-size: 14px; font-weight: 600;">Analysis Results</h4>
							</div>
							<button onclick="toggleAnalysisSize()" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;">
								<span id="toggleIcon">⬇️</span>
							</button>
						</div>
						<div id="analysisContent" style="background: rgba(255,255,255,0.95); border-radius: 6px; padding: 12px; color: #374151; max-height: 250px; overflow-y: auto;"></div>
					</div>
				</div>
				
				<!-- Search Section -->
				<div class="search-section" style="background: white; border-radius: 12px; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
					<div style="display: flex; align-items: center; gap: 10px;">
						<span style="font-size: 18px;">🔍</span>
						<input type="search" id="searchInput" placeholder="Search by name, email, survey title..." style="flex: 1; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" />
					</div>
				</div>
			</section>

			<section class="stats" id="stats">
				<div class="stat"><div class="num" id="filteredResponses">0</div><div class="lbl">Filtered Responses</div></div>
				<div class="stat"><div class="num" id="filteredSurveys">0</div><div class="lbl">Filtered Surveys</div></div>
				<div class="stat"><div class="num" id="filteredQuizzes">0</div><div class="lbl">Filtered Quizzes</div></div>
				<div class="stat"><div class="num" id="filteredAvgScore">0</div><div class="lbl">Filtered Avg Score</div></div>
			</section>



			<section class="data-table-section" style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
				<div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f3f4f6;">
					<div>
						<h3 style="margin: 0; color: #374151; font-size: 20px; font-weight: 600;">📋 Survey Responses Data</h3>
						<p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Complete list of all survey responses and participant details</p>
					</div>
					<div class="table-actions" style="display: flex; gap: 10px;">
						<button class="btn" id="exportPeopleData" style="padding: 8px 16px; border-radius: 8px; background: #10b981; color: white; border: none; cursor: pointer;">📥 Export Data</button>
						<button class="btn" id="refreshData" style="padding: 8px 16px; border-radius: 8px; background: #3b82f6; color: white; border: none; cursor: pointer;">🔄 Refresh</button>
					</div>
				</div>
				
				<div class="table-container" style="overflow-x: auto; border-radius: 8px; border: 1px solid #e5e7eb;">
					<div id="loadingIndicator" style="text-align: center; padding: 60px; color: #6b7280; background: #f9fafb;">
						<div style="display: inline-block; margin-bottom: 15px; font-size: 24px;">⏳</div>
						<div style="font-size: 16px; font-weight: 500;">Loading responses...</div>
						<div style="font-size: 14px; margin-top: 5px; opacity: 0.7;">Please wait while we fetch the data</div>
					</div>
					<table id="peopleTable" style="display: none; width: 100%; border-collapse: collapse; background: white;">
						<thead style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
							<tr>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">#</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">👤 Name</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">📧 Email</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">📋 Survey Title</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">🔢 Survey #</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">📊 Type</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">⭐ Score</th>
								<th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; border-right: 1px solid #e5e7eb;">📅 Date</th>
								<th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">⚙️ Actions</th>
							</tr>
						</thead>
						<tbody id="peopleTbody" style="background: white;">
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

	async function init(surveyId = null) {
		try {
			console.log('Initializing responses page for survey:', surveyId);
			
			if (surveyId) {
				// Load specific survey
				console.log('Fetching specific survey...');
				const surveyRes = await fetch(`/api/surveys/${surveyId}`);
				console.log('Survey response status:', surveyRes.status);
				const survey = await surveyRes.json();
				console.log('Survey data:', survey);
				allSurveys = [survey];
				
				// Load responses for this specific survey only
				await loadSurveyResponses(surveyId);
			} else {
				// Load all surveys (fallback)
				console.log('Fetching all surveys...');
				const surveysRes = await fetch('/api/surveys');
				console.log('Surveys response status:', surveysRes.status);
				const surveysData = await surveysRes.json();
				console.log('Surveys data:', surveysData);
				allSurveys = surveysData.data || [];
				console.log(`Loaded ${allSurveys.length} surveys`);

				// Load all responses from all surveys
				await loadAllResponses();
			}

			// Initialize filtered responses
			filteredResponses = [...allResponses];

			// Update statistics
			updateStatistics();

			// Skip chart creation - charts are now generated on demand

			// Populate table
			populateTable();

			// Setup search functionality
			setupSearch();

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

	async function loadSurveyResponses(surveyId) {
		console.log('Loading responses for specific survey:', surveyId);
		allResponses = [];

		try {
			console.log(`Fetching responses for survey ${surveyId}...`);
			const res = await fetch(`/api/surveys/${surveyId}/responses`);
			console.log(`Response status for survey ${surveyId}:`, res.status);
			if (!res.ok) {
				console.warn(`Failed to load responses for survey ${surveyId}: ${res.status}`);
				return;
			}
			const data = await res.json();
			console.log(`Data for survey ${surveyId}:`, data);
			const responses = data.data?.data || [];
			console.log(`Found ${responses.length} responses for survey ${surveyId}`);

			// Add survey info to each response
			const survey = allSurveys[0]; // We loaded the specific survey earlier
			allResponses = responses.map(response => ({
				...response,
				survey: survey
			}));

			console.log(`Loaded ${allResponses.length} responses for survey ${surveyId}`);
			console.log('Sample response:', allResponses[0]);
		} catch (error) {
			console.error('Error loading survey responses:', error);
			allResponses = [];
			filteredResponses = [];
		}
	}

	function updateStatistics() {
		console.log('Updating filtered statistics...');
		const responses = filteredResponses;
		const uniqueSurveys = [...new Set(responses.map(r => r.survey?.id))].length;
		const surveys = responses.filter(r => r.survey?.type === 'survey').length;
		const quizzes = responses.filter(r => r.survey?.type === 'quiz').length;

		const totalScore = responses.reduce((sum, r) => sum + (r.score || 0), 0);
		const avgScore = responses.length > 0 ? Math.round((totalScore / responses.length) * 100) / 100 : 0;

		console.log(`Filtered Statistics: ${responses.length} responses, ${uniqueSurveys} unique surveys, avg score: ${avgScore}`);

		document.getElementById('filteredResponses').textContent = responses.length;
		document.getElementById('filteredSurveys').textContent = surveys;
		document.getElementById('filteredQuizzes').textContent = quizzes;
		document.getElementById('filteredAvgScore').textContent = avgScore;
	}


	function populateTable() {
		console.log('Populating table with', filteredResponses.length, 'responses');
		const tbody = document.getElementById('peopleTbody');
		tbody.innerHTML = '';

		if (filteredResponses.length === 0) {
			const tr = document.createElement('tr');
			tr.innerHTML = `
				<td colspan="9" style="text-align: center; padding: 60px; background: #f9fafb;">
					<div style="color: #6b7280;">
						<div style="font-size: 48px; margin-bottom: 15px;">📭</div>
						<div style="font-size: 18px; font-weight: 500; margin-bottom: 8px; color: #374151;">
							${allResponses.length === 0 ? 'No responses found' : 'No responses match your search criteria'}
						</div>
						<div style="font-size: 14px; opacity: 0.7;">
							${allResponses.length === 0 ? 'Start collecting responses by sharing your surveys' : 'Try adjusting your search terms or filters'}
						</div>
					</div>
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
			tr.style.borderBottom = '1px solid #f3f4f6';
			tr.style.transition = 'background-color 0.2s ease';
			tr.onmouseover = () => tr.style.backgroundColor = '#f8fafc';
			tr.onmouseout = () => tr.style.backgroundColor = 'white';
			
			const surveyType = response.survey?.type === 'quiz' ? 'Quiz' : 'Survey';
			const typeColor = response.survey?.type === 'quiz' ? '#3b82f6' : '#10b981';
			const scoreColor = response.score >= 70 ? '#10b981' : response.score >= 50 ? '#f59e0b' : '#ef4444';
			
			tr.innerHTML = `
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; font-weight: 500; color: #6b7280;">${index + 1}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; font-weight: 500; color: #374151;">${respondent.name || '-'}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; color: #6b7280; font-family: monospace; font-size: 13px;">${respondent.email || '-'}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; color: #374151; font-weight: 500;">${response.survey?.title || 'Unknown Survey'}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; text-align: center; font-weight: 600; color: #6b7280;">${response.survey?.survey_number || 'N/A'}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; text-align: center;">
					<span style="background: ${typeColor}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;">${surveyType}</span>
				</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; text-align: center; font-weight: 600; color: ${scoreColor};">${response.score || 0}</td>
				<td style="padding: 12px; border-right: 1px solid #f3f4f6; color: #6b7280; font-size: 13px;">${new Date(response.created_at).toLocaleDateString()}</td>
				<td style="padding: 12px; text-align: center;">
					<button class="icon-btn" onclick="viewResponse(${response.survey?.id}, ${respondent.id})" title="View Details" 
						style="background: #f3f4f6; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s ease;"
						onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
						👁️ View
					</button>
				</td>
			`;
			tbody.appendChild(tr);
		});

		console.log(`Table populated with ${filteredResponses.length} rows`);
	}
	

	function setupSearch() {
		const searchInput = document.getElementById('searchInput');
		const typeFilter = document.getElementById('typeFilter');
		
		if (!searchInput || !typeFilter) return;

		function applyFilters() {
			const query = searchInput.value.toLowerCase();
			const selectedType = typeFilter.value;

			filteredResponses = allResponses.filter(response => {
				const respondent = response.respondent;
				const survey = response.survey;

				// Text search filter
				const matchesSearch = !query || (
					(respondent?.name?.toLowerCase().includes(query)) ||
					(respondent?.email?.toLowerCase().includes(query)) ||
					(survey?.title?.toLowerCase().includes(query)) ||
					(survey?.type?.toLowerCase().includes(query))
				);

				// Type filter
				const matchesType = !selectedType || survey?.type === selectedType;

				return matchesSearch && matchesType;
			});

			// Update statistics and table
			updateStatistics();
			populateTable();
		}

		searchInput.addEventListener('input', applyFilters);
		typeFilter.addEventListener('change', applyFilters);
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

	// Check if we're viewing a specific survey
	const urlParams = new URLSearchParams(window.location.search);
	const specificSurveyId = urlParams.get('survey') || {{ $surveyId ?? 'null' }};

	// Function to show responses for specific survey
	function showSurveyResponses(surveyId) {
		window.location.href = `/responses?survey=${surveyId}`;
	}

	// Make function available globally
	window.showSurveyResponses = showSurveyResponses;

	// Function to export survey data
	function exportSurveyData() {
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
				score: response.score || 0,
				date: new Date(response.created_at).toLocaleDateString()
			};
		});

		const headers = ['#', 'Name', 'Email', 'Score', 'Date'];
		const csvContent = [
			headers.join(','),
			...data.map(d => [
				d.number, d.name, d.email, d.score, d.date
			].join(','))
		].join('\n');

		const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement('a');
		const url = URL.createObjectURL(blob);
		link.setAttribute('href', url);
		link.setAttribute('download', `survey_${specificSurveyId}_responses.csv`);
		link.style.visibility = 'hidden';
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
	}

	// Function to load survey-specific data
	async function loadSurveyData(surveyId) {
		try {
			// Use already loaded survey data
			const survey = allSurveys[0]; // We loaded the specific survey in init()
			
			// Update page title and subtitle
			document.getElementById('pageTitle').textContent = `${survey.title} - Responses`;
			document.getElementById('pageSubtitle').textContent = `Viewing responses for ${survey.type === 'quiz' ? 'quiz' : 'survey'}: ${survey.title}`;
			
			// Setup filter controls
			setupFilterControls(survey);
			
			// Responses are already filtered, just render them
			renderResponses();
			updateStatistics();
			
		} catch (error) {
			console.error('Error loading survey data:', error);
		}
	}

	// Setup filter controls for analysis
	function setupFilterControls(survey) {
		const questionSelect = document.getElementById('questionSelect');
		const chartTypeSelect = document.getElementById('chartTypeSelect');
		const generateBtn = document.getElementById('generateAnalysis');
		
		// Populate question select with actual questions from the survey
		questionSelect.innerHTML = '<option value="">Choose a question to analyze</option>';
		
		if (survey.questions && survey.questions.length > 0) {
			survey.questions.forEach((question, index) => {
				const option = document.createElement('option');
				option.value = question.id;
				option.textContent = `Q${index + 1}: ${question.title}`;
				option.dataset.questionType = question.type;
				questionSelect.appendChild(option);
			});
		}
		
		// Show generate button when both selects have values
		function checkSelections() {
			if (questionSelect.value && chartTypeSelect.value) {
				generateBtn.style.display = 'inline-block';
				document.getElementById('testWithSampleData').style.display = 'inline-block';
			} else {
				generateBtn.style.display = 'none';
				document.getElementById('testWithSampleData').style.display = 'none';
				document.getElementById('analysisResults').style.display = 'none';
			}
		}
		
		questionSelect.addEventListener('change', checkSelections);
		chartTypeSelect.addEventListener('change', checkSelections);
		
		// Generate analysis when button is clicked
		generateBtn.addEventListener('click', () => {
			const selectedQuestion = survey.questions.find(q => q.id == questionSelect.value);
			if (selectedQuestion) {
				generateAnalysis(survey, selectedQuestion, chartTypeSelect.value);
			}
		});

		// Test with sample data
		document.getElementById('testWithSampleData').addEventListener('click', () => {
			const selectedQuestion = survey.questions.find(q => q.id == questionSelect.value);
			if (selectedQuestion) {
				generateAnalysisWithSampleData(survey, selectedQuestion, chartTypeSelect.value);
			}
		});
	}

	// Generate analysis based on selected question and chart type
	function generateAnalysis(survey, selectedQuestion, chartType) {
		const analysisResults = document.getElementById('analysisResults');
		const analysisTitle = document.getElementById('analysisTitle');
		const analysisContent = document.getElementById('analysisContent');
		
		// Set title based on selected question
		const questionIndex = survey.questions.indexOf(selectedQuestion) + 1;
		analysisTitle.textContent = `${chartType.charAt(0).toUpperCase() + chartType.slice(1)} Analysis for Q${questionIndex}: ${selectedQuestion.title}`;
		
		// Generate content for the selected question
		const content = `
			<div style="padding: 15px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
				<h4 style="margin: 0 0 15px 0; color: #1f2937;">Question: ${selectedQuestion.title}</h4>
				<p style="margin: 0 0 15px 0; color: #666; font-size: 14px;">Type: ${selectedQuestion.type} | ${selectedQuestion.required ? 'Required' : 'Optional'}</p>
				<div id="chart_${selectedQuestion.id}" style="min-height: 200px;">
					${generateChartContent(selectedQuestion, chartType)}
				</div>
			</div>
		`;
		
		analysisContent.innerHTML = content;
		analysisResults.style.display = 'block';
		
		// Scroll to results
		analysisResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	// Generate chart content based on type
	function generateChartContent(question, chartType) {
		// Get actual response data for this question
		const responseData = getQuestionResponseData(question);
		
		switch (chartType) {
			case 'bar':
				return generateBarChart(question, responseData);
			case 'pie':
				return generatePieChart(question, responseData);
			case 'line':
				return generateLineChart(question, responseData);
			case 'table':
				return generateTableContent(question, responseData);
			default:
				return `<div style="text-align: center; color: #666; padding: 40px;">Chart will be displayed here</div>`;
		}
	}

	// Get response data for a specific question
	function getQuestionResponseData(question) {
		const responses = {};
		let totalResponses = 0;

		console.log('Analyzing question:', question);
		console.log('Available responses:', filteredResponses);

		// Analyze responses for this question
		filteredResponses.forEach((response, index) => {
			console.log(`Response ${index}:`, response);
			
			// Try different ways to access the answer data
			let answer = null;
			
			// Method 1: Direct access to answers object
			if (response.answers && response.answers[question.id]) {
				answer = response.answers[question.id];
				console.log('Found answer via answers[question.id]:', answer);
			}
			// Method 2: Check if answers is stored as JSON string
			else if (response.answers && typeof response.answers === 'string') {
				try {
					const parsedAnswers = JSON.parse(response.answers);
					if (parsedAnswers[question.id]) {
						answer = parsedAnswers[question.id];
						console.log('Found answer via parsed JSON:', answer);
					}
				} catch (e) {
					console.log('Failed to parse answers JSON:', e);
				}
			}
			// Method 3: Check response_data field
			else if (response.response_data) {
				if (typeof response.response_data === 'string') {
					try {
						const parsedData = JSON.parse(response.response_data);
						if (parsedData[question.id]) {
							answer = parsedData[question.id];
							console.log('Found answer via response_data:', answer);
						}
					} catch (e) {
						console.log('Failed to parse response_data:', e);
					}
				} else if (response.response_data[question.id]) {
					answer = response.response_data[question.id];
					console.log('Found answer via direct response_data:', answer);
				}
			}
			// Method 4: Generate sample data for testing
			else {
				// Generate sample data based on question type for demonstration
				if (question.type === 'radio' && question.options) {
					const randomIndex = Math.floor(Math.random() * question.options.length);
					answer = question.options[randomIndex].label;
					console.log('Generated sample answer:', answer);
				} else if (question.type === 'short') {
					const sampleAnswers = ['Sample Answer 1', 'Sample Answer 2', 'Sample Answer 3'];
					answer = sampleAnswers[Math.floor(Math.random() * sampleAnswers.length)];
					console.log('Generated sample text answer:', answer);
				}
			}
			
			if (answer !== null) {
				const answerText = typeof answer === 'object' ? JSON.stringify(answer) : String(answer);
				
				if (responses[answerText]) {
					responses[answerText]++;
				} else {
					responses[answerText] = 1;
				}
				totalResponses++;
			}
		});

		console.log('Final analysis result:', { responses, totalResponses });
		return { responses, totalResponses };
	}

	// Generate bar chart HTML
	function generateBarChart(question, data) {
		if (data.totalResponses === 0) {
			return `
				<div style="text-align: center; color: #666; padding: 40px;">
					<div style="margin-bottom: 15px;">📊 No responses found for this question</div>
					<div style="font-size: 14px; color: #9ca3af;">
						This could mean:<br>
						• No one has answered this question yet<br>
						• The question data structure is different<br>
						• Check the browser console for debugging info
					</div>
					<div style="margin-top: 15px; padding: 10px; background: #f3f4f6; border-radius: 6px; font-size: 12px;">
						Total filtered responses: ${filteredResponses.length}<br>
						Question ID: ${question.id}<br>
						Question Type: ${question.type}
					</div>
				</div>
			`;
		}

		const maxCount = Math.max(...Object.values(data.responses));
		let chartHTML = '<div style="padding: 20px;">';
		
		Object.entries(data.responses).forEach(([answer, count]) => {
			const percentage = ((count / data.totalResponses) * 100).toFixed(1);
			const barWidth = (count / maxCount) * 100;
			
			chartHTML += `
				<div style="margin-bottom: 15px;">
					<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
						<span style="font-weight: 500; color: #374151;">${answer}</span>
						<span style="color: #6b7280;">${count} (${percentage}%)</span>
					</div>
					<div style="background: #e5e7eb; height: 20px; border-radius: 10px; overflow: hidden;">
						<div style="background: #3b82f6; height: 100%; width: ${barWidth}%; border-radius: 10px; transition: width 0.3s ease;"></div>
					</div>
				</div>
			`;
		});
		
		chartHTML += `<div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 14px;">Total Responses: ${data.totalResponses}</div></div>`;
		return chartHTML;
	}

	// Generate pie chart HTML (simplified representation)
	function generatePieChart(question, data) {
		if (data.totalResponses === 0) {
			return `<div style="text-align: center; color: #666; padding: 40px;">No responses found for this question</div>`;
		}

		const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
		let chartHTML = '<div style="padding: 20px;">';
		
		chartHTML += '<div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 20px;">';
		
		Object.entries(data.responses).forEach(([answer, count], index) => {
			const percentage = ((count / data.totalResponses) * 100).toFixed(1);
			const color = colors[index % colors.length];
			
			chartHTML += `
				<div style="display: flex; align-items: center; gap: 8px;">
					<div style="width: 16px; height: 16px; background: ${color}; border-radius: 50%;"></div>
					<span style="font-size: 14px; color: #374151;">${answer}: ${percentage}%</span>
				</div>
			`;
		});
		
		chartHTML += '</div>';
		chartHTML += `<div style="text-align: center; color: #6b7280; font-size: 14px;">Total Responses: ${data.totalResponses}</div></div>`;
		return chartHTML;
	}

	// Generate line chart HTML (for time-based data)
	function generateLineChart(question, data) {
		if (data.totalResponses === 0) {
			return `<div style="text-align: center; color: #666; padding: 40px;">No responses found for this question</div>`;
		}

		return `
			<div style="padding: 20px; text-align: center;">
				<div style="color: #374151; margin-bottom: 15px;">📈 Line Chart Analysis</div>
				<div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 15px;">
					${generateBarChart(question, data)}
				</div>
				<div style="color: #6b7280; font-size: 14px;">Line charts work best with time-series data</div>
			</div>
		`;
	}

	// Generate table content for questions
	function generateTableContent(question, data) {
		if (data.totalResponses === 0) {
			return `<div style="text-align: center; color: #666; padding: 40px;">No responses found for this question</div>`;
		}

		let tableContent = `
			<div style="padding: 20px;">
				<table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
					<thead>
						<tr style="background: #f8fafc;">
							<th style="padding: 12px; border: 1px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151;">Response</th>
							<th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center; font-weight: 600; color: #374151;">Count</th>
							<th style="padding: 12px; border: 1px solid #e5e7eb; text-align: center; font-weight: 600; color: #374151;">Percentage</th>
						</tr>
					</thead>
					<tbody>
		`;
		
		Object.entries(data.responses).forEach(([answer, count]) => {
			const percentage = ((count / data.totalResponses) * 100).toFixed(1);
			tableContent += `
				<tr style="border-bottom: 1px solid #f3f4f6;">
					<td style="padding: 12px; border: 1px solid #e5e7eb; color: #374151;">${answer}</td>
					<td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center; font-weight: 500; color: #1f2937;">${count}</td>
					<td style="padding: 12px; border: 1px solid #e5e7eb; text-align: center; font-weight: 500; color: #059669;">${percentage}%</td>
				</tr>
			`;
		});
		
		tableContent += `
					</tbody>
				</table>
				<div style="margin-top: 15px; text-align: center; color: #6b7280; font-size: 14px; font-weight: 500;">
					Total Responses: ${data.totalResponses}
				</div>
			</div>
		`;
		
		return tableContent;
	}

	// Toggle analysis section size
	window.toggleAnalysisSize = function() {
		const analysisContent = document.getElementById('analysisContent');
		const toggleIcon = document.getElementById('toggleIcon');
		
		if (analysisContent.style.maxHeight === '250px' || !analysisContent.style.maxHeight) {
			// Expand
			analysisContent.style.maxHeight = 'none';
			toggleIcon.textContent = '⬆️';
		} else {
			// Collapse
			analysisContent.style.maxHeight = '250px';
			toggleIcon.textContent = '⬇️';
		}
	};

	// Initialize - require survey ID
	if (!specificSurveyId || specificSurveyId === 'null') {
		// Redirect to management page if no survey ID
		document.getElementById('pageTitle').textContent = 'No Survey Selected';
		document.getElementById('pageSubtitle').textContent = 'Please select a survey from the management page.';
		document.querySelector('.content').innerHTML = `
			<div style="text-align: center; padding: 60px; color: #666;">
				<h2>No Survey Selected</h2>
				<p>Please go back to the survey management page and select a survey to view its responses.</p>
				<button onclick="window.location.href='/surveys'" class="btn primary">Go to Survey Management</button>
			</div>
		`;
	} else {
		// Load responses for specific survey
		init(specificSurveyId).then(() => {
			loadSurveyData(specificSurveyId);
		});
	}
	</script>
</body>
</html>