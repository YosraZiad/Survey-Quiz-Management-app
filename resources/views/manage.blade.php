<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Manage Surveys</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
	<style>
		/* Make this page use two columns (sidebar + content) */
		.app-two { grid-template-columns: 260px 1fr !important; }

		/* Let the content stretch edge-to-edge within the content area */
		.builder { padding: 0; }
		.container { max-width: none; margin: 0; padding: 22px; }

		.container {
			max-width: 1200px;
			margin: 20px auto;
			padding: 0 20px;
		}
		
		.toolbar {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 30px;
			padding-bottom: 20px;
			border-bottom: 1px solid #e5e7eb;
		}
		
		.toolbar h2 {
			font-size: 28px;
			font-weight: 700;
			color: #1f2937;
			margin: 0;
		}
		
		.table-container {
			background: white;
			border-radius: 12px;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
			border: 1px solid #e5e7eb;
			overflow: hidden;
		}
		
		.table {
			width: 100%;
			border-collapse: collapse;
		}
		
		.table th {
			background: #f8fafc;
			padding: 16px;
			text-align: left;
			font-weight: 600;
			color: #374151;
			border-bottom: 1px solid #e5e7eb;
			font-size: 14px;
		}
		
		.table td {
			padding: 16px;
			border-bottom: 1px solid #f3f4f6;
			color: #6b7280;
			font-size: 14px;
		}
		
		.table tr:hover {
			background: #f9fafb;
		}
		
		.table tr:last-child td {
			border-bottom: none;
		}
		
		.survey-title {
			font-weight: 600;
			color: #1f2937;
			margin-bottom: 4px;
		}
		
		.survey-id {
			font-size: 12px;
			color: #9ca3af;
		}
		
		.type-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 8px;
			border-radius: 6px;
			font-size: 12px;
			font-weight: 500;
			background: #f3f4f6;
			color: #6b7280;
		}
		
		.status-badge {
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 500;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		.status-badge.published {
			background: #ecfdf5;
			color: #065f46;
			border: 1px solid #a7f3d0;
		}
		
		.status-badge.draft {
			background: #fef3c7;
			color: #92400e;
			border: 1px solid #fcd34d;
		}
		
		.actions {
			display: flex;
			gap: 6px;
			flex-wrap: wrap;
		}
		
		.btn {
			padding: 6px 12px;
			border-radius: 6px;
			font-size: 12px;
			font-weight: 500;
			text-decoration: none;
			border: 1px solid #d1d5db;
			background: white;
			color: #374151;
			cursor: pointer;
			transition: all 0.2s ease;
			display: inline-flex;
			align-items: center;
			gap: 4px;
		}
		
		.btn:hover {
			background: #f9fafb;
			border-color: #9ca3af;
		}
		
		.btn.primary {
			background: #3b82f6;
			color: white;
			border-color: #3b82f6;
		}
		
		.btn.primary:hover {
			background: #2563eb;
			border-color: #2563eb;
		}
		
		.btn.success {
			background: #10b981;
			color: white;
			border-color: #10b981;
		}
		
		.btn.success:hover {
			background: #059669;
			border-color: #059669;
		}
		
		.btn.danger {
			background: #ef4444;
			color: white;
			border-color: #ef4444;
		}
		
		.btn.danger:hover {
			background: #dc2626;
			border-color: #dc2626;
		}
		
		.empty-state {
			text-align: center;
			padding: 60px 20px;
			color: #6b7280;
		}
		
		.empty-state h3 {
			font-size: 20px;
			margin-bottom: 8px;
			color: #374151;
		}
		
		.empty-state p {
			margin-bottom: 24px;
		}
		
		.loading {
			text-align: center;
			padding: 40px;
			color: #6b7280;
		}
		
		@media (max-width: 768px) {
			.table-container {
				overflow-x: auto;
			}
			
			.toolbar {
				flex-direction: column;
				gap: 16px;
				align-items: stretch;
			}
			
			.table th,
			.table td {
				padding: 12px 8px;
				font-size: 13px;
			}
			
			.actions {
				flex-direction: column;
				gap: 4px;
			}
		}
	</style>
</head>
<body>
	<div class="app app-two">
		<!-- Sidebar -->
		<aside class="sidebar">
			<div class="brand">
				<div class="brand-title">AQL Soft</div>
				<div class="brand-sub">Survey & Quiz Management</div>
			</div>
			<nav class="menu">
				<a class="menu-item" href="{{ url('/dashboard') }}">📊 <span>Dashboard</span></a>
				<a class="menu-item" href="{{ url('/') }}">📝 <span>Survey Builder</span></a>
				<a class="menu-item" href="{{ url('/responses') }}">👁️ <span>View Responses</span></a>
				<!-- <a class="menu-item" href="{{ url('/analytics') }}">📈 <span>Analytics</span></a>
				<a class="menu-item" href="#">👥 <span>User Management</span></a> -->
			</nav>
		</aside>

		<main class="builder">
			<div class="container">
		<div class="toolbar">
			<h2>📊 Survey Management</h2>
			<a class="btn primary" href="/">
				<span>➕</span>
				New Survey
			</a>
		</div>
		
		<div class="loading" id="loading">
			<div>Loading surveys...</div>
		</div>
		
		<div class="table-container" id="tableContainer" style="display: none;">
			<table class="table" id="surveysTable">
				<thead>
					<tr>
						<th>Survey</th>
						<th>Type</th>
						<th>Status</th>
						<th>Last Updated</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
			</table>
		</div>
		
		<div class="empty-state" id="emptyState" style="display: none;">
			<h3>No surveys found</h3>
			<p>Create your first survey to get started</p>
			<a class="btn primary" href="/">Create Survey</a>
		</div>
			</div>
		</main>
	</div>

	<script>
	async function loadList(){
		const loading = document.getElementById('loading');
		const tableContainer = document.getElementById('tableContainer');
		const tableBody = document.getElementById('tableBody');
		const emptyState = document.getElementById('emptyState');
		
		try {
			const res = await fetch('/api/surveys');
			const json = await res.json();
			const data = json.data || json.data?.data || [];
			
			loading.style.display = 'none';
			
			if (data.length === 0) {
				emptyState.style.display = 'block';
				return;
			}
			
			tableContainer.style.display = 'block';
			tableBody.innerHTML = '';
			
			data.forEach(survey => {
				const row = createSurveyRow(survey);
				tableBody.appendChild(row);
			});
		} catch (error) {
			loading.innerHTML = '<div style="color: #ef4444;">Failed to load surveys</div>';
		}
	}

	function createSurveyRow(survey) {
		const row = document.createElement('tr');
		
		const typeIcon = survey.type === 'quiz' ? '🎯' : '📊';
		const statusClass = survey.is_published ? 'published' : 'draft';
		const statusText = survey.is_published ? 'Published' : 'Draft';
		
		row.innerHTML = `
			<td>
				<div class="survey-title">${survey.title}</div>
				<div class="survey-id">ID: ${survey.id}</div>
			</td>
			<td>
				<div class="type-badge">
					<span>${typeIcon}</span>
					<span>${survey.type.charAt(0).toUpperCase() + survey.type.slice(1)}</span>
				</div>
			</td>
			<td>
				<span class="status-badge ${statusClass}">${statusText}</span>
			</td>
			<td>
				${new Date(survey.updated_at).toLocaleDateString('en-US', {
					year: 'numeric',
					month: 'short',
					day: 'numeric',
					hour: '2-digit',
					minute: '2-digit'
				})}
			</td>
			<td>
				<div class="actions">
					<a class="btn" href="/?survey=${survey.id}">
						<span>✏️</span>
						Edit
					</a>
					<a class="btn" href="/preview/${survey.id}">
						<span>👁️</span>
						Preview
					</a>
					<a class="btn" href="/responses?survey=${survey.id}">
						<span>📊</span>
						Responses
					</a>
					${survey.is_published 
						? `<a class="btn success" href="/s/${survey.id}" target="_blank">
							<span>🔗</span>
							Open
						</a>` 
						: `<button class="btn primary" onclick="publish(${survey.id})">
							<span>🚀</span>
							Publish
						</button>`
					}
					<button class="btn danger" onclick="deleteSurvey(${survey.id})">
						<span>🗑️</span>
						Delete
					</button>
				</div>
			</td>
		`;
		
		return row;
	}

	async function publish(id){
		try {
			const r = await fetch(`/api/surveys/${id}/publish`, {method:'POST'});
			if(r.ok){ 
				loadList(); 
			} else { 
				alert('Failed to publish survey'); 
			}
		} catch (error) {
			alert('Failed to publish survey');
		}
	}

	async function deleteSurvey(id){
		if(!confirm('Are you sure you want to delete this survey? This action cannot be undone.')) return;
		
		try {
			const r = await fetch(`/api/surveys/${id}`, {method:'DELETE'});
			if(r.ok){ 
				loadList(); 
			} else { 
				alert('Failed to delete survey'); 
			}
		} catch (error) {
			alert('Failed to delete survey');
		}
	}

	// Load surveys on page load
	loadList();
	</script>
</body>
</html>
