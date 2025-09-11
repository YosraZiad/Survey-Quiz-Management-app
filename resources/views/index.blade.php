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
				<div class="brand-title">Academic Platform</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item active" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item" href="{{ url('/responses') }}">👁️ <span>View Responses</span></a>
				<a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a>
			</nav>
		</aside>

		<!-- Field types list -->
		<section class="field-types">
			<h3 class="section-title">Field Types</h3>
			<div class="field-list" id="fieldList">
				<!-- Cards populated by JS to keep one source of truth -->
			</div>
			
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
					<div class="stat-card" id="pointsCard" style="display: none;">
						<div class="stat-icon">🎯</div>
						<div class="stat-info">
							<div class="stat-value" id="totalPoints">0</div>
							<div class="stat-label">Total Points</div>
						</div>
					</div>
					<div class="stat-card" id="weightsCard" style="display: none;">
						<div class="stat-icon">⚖️</div>
						<div class="stat-info">
							<div class="stat-value" id="maxWeight">0</div>
							<div class="stat-label">Max Weight</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Builder canvas -->
		<main class="builder">
			<div style="display:flex; justify-content: flex-end; margin-bottom: 10px; gap: 8px;">
				<form id="wordImportForm" enctype="multipart/form-data" style="display:flex; align-items:center; gap:8px;">
					<input type="file" id="wordFile" name="file" accept=".docx,.txt" />
					<select id="importType">
						<option value="survey">Survey</option>
						<option value="quiz">Quiz</option>
					</select>
					<button type="submit" class="btn">📥 Import from Word</button>
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
					<label class="type-option">
						<input type="radio" name="surveyType" value="manage">
						<span class="type-card">
							<div class="type-icon">⚙️</div>
							<div class="type-title">Manage Surveys</div>
							<div class="type-desc">View and manage existing surveys</div>
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

	<script src="{{ asset('assets/js/script.js') }}"></script>
	<script>
		// Handle survey type selection
		document.addEventListener('DOMContentLoaded', function() {
			const surveyTypeInputs = document.querySelectorAll('input[name="surveyType"]');
			
			surveyTypeInputs.forEach(input => {
				input.addEventListener('change', function() {
					if (this.value === 'manage') {
						// Redirect to manage surveys page
						window.location.href = '{{ url("/surveys") }}';
					}
				});
			});
		});
	</script>
</body>
</html>
