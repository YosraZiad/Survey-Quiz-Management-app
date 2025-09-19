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
			overflow: visible;
			margin-bottom: 40px;
		}
		
		.table {
			width: 100%;
			border-collapse: collapse;
			background: white;
		}
		
		.table th {
			background: #f8fafc;
			padding: 8px 16px;
			text-align: left;
			font-weight: 600;
			color: #374151;
			border-bottom: 1px solid #e5e7eb;
			font-size: 14px;
		}
		
		.table td {
			padding: 8px 16px;
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
			border: none;
			background: none;
			padding: 0;
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
			position: relative;
			display: inline-block;
		}

		.dropdown {
			position: relative;
			display: inline-block;
		}

		.dropdown-toggle {
			padding: 8px 12px;
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

		.dropdown-toggle:hover {
			background: #f9fafb;
			border-color: #9ca3af;
		}

		.dropdown-menu {
			position: absolute;
			top: 100%;
			right: 0;
			background: white;
			border: 1px solid #d1d5db;
			border-radius: 8px;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
			min-width: 160px;
			z-index: 1000;
			display: none;
			padding: 4px 0;
			max-height: 300px;
			overflow-y: auto;
		}

		.dropdown-menu.show {
			display: block;
		}

		.dropdown-item {
			display: block;
			padding: 8px 12px;
			color: #374151;
			text-decoration: none;
			font-size: 13px;
			transition: background-color 0.2s ease;
			border: none;
			background: none;
			width: 100%;
			text-align: left;
			cursor: pointer;
		}

		.dropdown-item:hover {
			background: #f3f4f6;
		}

		.dropdown-item.danger {
			color: #dc2626;
		}

		.dropdown-item.danger:hover {
			background: #fef2f2;
		}

		.dropdown-item.disabled {
			color: #9ca3af !important;
			cursor: not-allowed !important;
			opacity: 0.6;
		}

		.dropdown-item.disabled:hover {
			background: none !important;
		}

		.toggle-active-btn {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 8px;
			border-radius: 6px;
			font-size: 12px;
			font-weight: 500;
			border: 1px solid;
			background: white;
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.toggle-active-btn.active {
			color: #065f46;
			border-color: #a7f3d0;
			background: #ecfdf5;
		}

		.toggle-active-btn.inactive {
			color: #92400e;
			border-color: #fcd34d;
			background: #fef3c7;
		}

		.toggle-active-btn:hover {
			opacity: 0.8;
		}

		.dropdown-item.success {
			color: #059669;
		}

		.dropdown-item.success:hover {
			background: #f0fdf4;
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
				<a class="menu-item" href="{{ url('/surveys') }}">⚙️ <span>Manage Surveys</span></a>
			</nav>
		</aside>

		<main class="builder">
			<div class="container">
		<div class="toolbar">
			<div style="display: flex; align-items: center; gap: 15px;">
				<!-- <button onclick="window.location.href='/'" class="btn" style="background: #6b7280; color: white;">← Back to Builder</button> -->
				<h2>📊 Survey Management</h2>
			</div>
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
						<th>#</th>
						<th>Survey</th>
						<th>Description</th>
						<th>Type</th>
						<th>Status</th>
						<th>Active</th>
						<th>Last Updated</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
			</table>
			<!-- Pagination Controls -->
			<div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
				<div class="pagination-info">
					<span id="pagination-info">Showing 1-10 of 0 surveys</span>
				</div>
				<div class="pagination-controls">
					<button id="prev-page" class="btn btn-secondary" disabled>
						<i class="fas fa-chevron-left"></i> Previous
					</button>
					<span id="page-numbers" style="margin: 0 15px;"></span>
					<button id="next-page" class="btn btn-secondary" disabled>
						Next <i class="fas fa-chevron-right"></i>
					</button>
				</div>
			</div>
		</div>
	</div>	
		<div class="empty-state" id="emptyState" style="display: none;">
			<h3>No surveys found</h3>
			<p>Create your first survey to get started</p>
			<a class="btn primary" href="/">Create Survey</a>
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
		static show(message, title = 'تأكيد الإجراء', options = {}) {
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

				const confirmText = options.confirmText || 'تأكيد';
				const cancelText = options.cancelText || 'إلغاء';
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

	let surveys = [];
	let filteredSurveys = [];
	let currentPage = 1;
	const surveysPerPage = 10;

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
			
			surveys = data;
			filteredSurveys = surveys;
			renderSurveys();
		} catch (error) {
			loading.innerHTML = '<div style="color: #ef4444;">Failed to load surveys</div>';
		}
	}

	function renderSurveys() {
		const tbody = document.getElementById('tableBody');
		tbody.innerHTML = '';
		
		if (filteredSurveys.length === 0) {
			tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">No surveys found</td></tr>';
			updatePaginationInfo(0, 0, 0);
			updatePaginationControls(0);
			return;
		}
		
		// Calculate pagination
		const totalSurveys = filteredSurveys.length;
		const totalPages = Math.ceil(totalSurveys / surveysPerPage);
		const startIndex = (currentPage - 1) * surveysPerPage;
		const endIndex = Math.min(startIndex + surveysPerPage, totalSurveys);
		const currentPageSurveys = filteredSurveys.slice(startIndex, endIndex);
		
		currentPageSurveys.forEach((survey, index) => {
			const row = document.createElement('tr');
			const globalIndex = startIndex + index + 1;
			const typeIcon = survey.type === 'quiz' ? '🎯' : '📊';
			const statusClass = survey.is_published ? 'published' : 'draft';
			const statusText = survey.is_published ? 'Published' : 'Draft';
			
			row.innerHTML = `
				<td>
					<div class="survey-title">${globalIndex}</div>
				</td>
				<td>
					<div class="survey-title">${survey.title}</div>
				</td>
				<td>
					<div style="color: #6b7280; font-size: 13px;">${survey.description || 'No description'}</div>
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
					<button class="toggle-active-btn ${survey.is_active ? 'active' : 'inactive'}" onclick="toggleActive(${survey.id}, ${survey.is_active})">
						<span class="toggle-icon">${survey.is_active ? '🟢' : '🔴'}</span>
						<span class="toggle-text">${survey.is_active ? 'Open' : 'Closed'}</span>
					</button>
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
					<div class="dropdown">
						<button class="dropdown-toggle" onclick="toggleDropdown(${survey.id})">
							<span>⚙️</span>
							Actions
							<span>▼</span>
						</button>
						<div class="dropdown-menu" id="dropdown-${survey.id}">
							<a class="dropdown-item" href="/edit/${survey.id}">
								<span>✏️</span> Edit
							</a>
							${survey.is_active ? `
								<a class="dropdown-item" href="/preview/${survey.id}">
									<span>👁️</span> Preview
								</a>
							` : `
								<span class="dropdown-item disabled" title="Survey is closed">
									<span>👁️</span> Preview (Disabled)
								</span>
							`}
							<a class="dropdown-item" href="/responses?survey=${survey.id}">
								<span>📊</span> Responses
							</a>
							${survey.is_published && survey.is_active
								? `<a class="dropdown-item success" href="/s/${survey.id}" target="_blank">
									<span>🔗</span> Open Survey
								</a>
								<button class="dropdown-item" onclick="copyLink('${window.location.origin}/s/${survey.id}')">
									<span>📋</span> Copy Link
								</button>` 
								: survey.is_active
								? `<button class="dropdown-item success" onclick="publish(${survey.id})">
									<span>🚀</span> Publish
								</button>`
								: `<span class="dropdown-item disabled" title="Survey is closed">
									<span>🚀</span> Publish (Disabled)
								</span>`
							}
							<button class="dropdown-item danger" onclick="deleteSurvey(${survey.id})">
								<span>🗑️</span> Delete
							</button>
						</div>
					</div>
				</td>
			`;
			
			tbody.appendChild(row);
		});
		
		// Update pagination info and controls
		updatePaginationInfo(startIndex + 1, endIndex, totalSurveys);
		updatePaginationControls(totalPages);
	}

	async function publish(id){
		const confirmed = await confirmDialog.show(
			'Do you want to publish this survey? It will become available to the public after publishing.',
			'Confirm Publish',
			{
				confirmText: 'Publish',
				cancelText: 'Cancel',
				type: 'info'
			}
		);
		
		if (!confirmed) return;
		
		try {
			toast.info('Publishing survey...');
			const r = await fetch(`/api/surveys/${id}/publish`, {method:'POST'});
			if(r.ok){ 
				toast.success('Survey published successfully');
				loadList(); 
			} else { 
				toast.error('Failed to publish survey'); 
			}
		} catch (error) {
			toast.error('Error occurred while publishing survey');
		}
	}

	async function deleteSurvey(id){
		const confirmed = await confirmDialog.show(
			'Are you sure you want to delete this survey? This action cannot be undone.',
			'Confirm Delete',
			{
				confirmText: 'Delete',
				cancelText: 'Cancel',
				type: 'danger'
			}
		);
		
		if (!confirmed) return;
		
		try {
			toast.info('Deleting survey...');
			const r = await fetch(`/api/surveys/${id}`, {method:'DELETE'});
			if(r.ok){ 
				toast.success('Survey deleted successfully');
				loadList(); 
			} else { 
				toast.error('Failed to delete survey'); 
			}
		} catch (error) {
			toast.error('Error occurred while deleting survey');
		}
	}

	function toggleDropdown(surveyId) {
		// Close all other dropdowns
		document.querySelectorAll('.dropdown-menu').forEach(menu => {
			if (menu.id !== `dropdown-${surveyId}`) {
				menu.classList.remove('show');
			}
		});
		
		// Toggle current dropdown
		const dropdown = document.getElementById(`dropdown-${surveyId}`);
		dropdown.classList.toggle('show');
	}

	// Copy survey link to clipboard
	function copyLink(url) {
		// Check if toast is available
		if (typeof window.toast === 'undefined') {
			alert('تم نسخ الرابط بنجاح!');
			return;
		}
		
		navigator.clipboard.writeText(url).then(function() {
			window.toast.success('Link copied successfully!');
		}).catch(function(err) {
			// Fallback for older browsers
			try {
				const textArea = document.createElement('textarea');
				textArea.value = url;
				document.body.appendChild(textArea);
				textArea.select();
				document.execCommand('copy');
				document.body.removeChild(textArea);
				window.toast.success('Link copied successfully!');
			} catch (fallbackErr) {
				alert('Link copied successfully!');
			}
		});
	}

	function filterSurveys() {
		const searchTerm = document.getElementById('search').value.toLowerCase();
		const typeFilter = document.getElementById('type-filter').value;
		
		filteredSurveys = surveys.filter(survey => {
			const matchesSearch = survey.title.toLowerCase().includes(searchTerm) || 
							  (survey.description && survey.description.toLowerCase().includes(searchTerm));
			const matchesType = typeFilter === 'all' || survey.type === typeFilter;
			return matchesSearch && matchesType;
		});
		
		// Reset to first page when filtering
		currentPage = 1;
		renderSurveys();
	}

	// Pagination functions
	function updatePaginationInfo(start, end, total) {
		const paginationInfo = document.getElementById('pagination-info');
		if (total === 0) {
			paginationInfo.textContent = 'No surveys found';
		} else {
			paginationInfo.textContent = `Showing ${start}-${end} of ${total} surveys`;
		}
	}

	function updatePaginationControls(totalPages) {
		const prevBtn = document.getElementById('prev-page');
		const nextBtn = document.getElementById('next-page');
		const pageNumbers = document.getElementById('page-numbers');
		
		// Update button states
		prevBtn.disabled = currentPage === 1;
		nextBtn.disabled = currentPage === totalPages || totalPages === 0;
		
		// Update page numbers
		if (totalPages === 0) {
			pageNumbers.textContent = '';
		} else {
			pageNumbers.textContent = `Page ${currentPage} of ${totalPages}`;
		}
	}

	function goToPage(page) {
		const totalPages = Math.ceil(filteredSurveys.length / surveysPerPage);
		if (page >= 1 && page <= totalPages) {
			currentPage = page;
			renderSurveys();
		}
	}

	// Add event listeners for pagination
	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('prev-page').addEventListener('click', function() {
			if (currentPage > 1) {
				goToPage(currentPage - 1);
			}
		});
		
		document.getElementById('next-page').addEventListener('click', function() {
			const totalPages = Math.ceil(filteredSurveys.length / surveysPerPage);
			if (currentPage < totalPages) {
				goToPage(currentPage + 1);
			}
		});
	});

	// Close dropdown when clicking outside
	document.addEventListener('click', function(event) {
		if (!event.target.closest('.dropdown')) {
			document.querySelectorAll('.dropdown-menu').forEach(menu => {
				menu.classList.remove('show');
			});
		}
	});

	// Toggle survey active status
	async function toggleActive(surveyId, currentStatus) {
		try {
			const response = await fetch(`/api/surveys/${surveyId}/toggle-active`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					is_active: !currentStatus
				})
			});

			if (response.ok) {
				loadList(); // Reload the surveys list
			} else {
				alert('Failed to update survey status');
			}
		} catch (error) {
			alert('Failed to update survey status');
		}
	}

	// Load surveys on page load
	loadList();
	</script>
</body>
</html>
