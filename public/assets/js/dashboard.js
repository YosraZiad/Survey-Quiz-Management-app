// Dashboard Analytics Script
let dashboardData = null;

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', async () => {
	await loadDashboardData();
	renderCharts();
	renderRecentActivity();
	renderTopSurveys();
});

// Load all dashboard data from API
async function loadDashboardData() {
	try {
		const response = await fetch('/api/dashboard/stats');
		if (!response.ok) throw new Error('Failed to load dashboard data');
		dashboardData = await response.json();
		
		// Update stats cards
		document.getElementById('totalSurveys').textContent = dashboardData.totalSurveys || 0;
		document.getElementById('totalResponses').textContent = dashboardData.totalResponses || 0;
		document.getElementById('activeUsers').textContent = dashboardData.activeUsers || 0;
		document.getElementById('responseRate').textContent = `${dashboardData.responseRate || 0}%`;
		
	} catch (error) {
		console.error('Error loading dashboard data:', error);
		// Show fallback data
		document.getElementById('totalSurveys').textContent = '0';
		document.getElementById('totalResponses').textContent = '0';
		document.getElementById('activeUsers').textContent = '0';
		document.getElementById('responseRate').textContent = '0%';
	}
}

// Chart drawing functions
function drawLine(canvasId, data, color, fill) {
	const cv = document.getElementById(canvasId);
	if (!cv || !data || data.length === 0) return;
	
	const ctx = cv.getContext('2d');
	const w = cv.width = cv.clientWidth;
	const h = cv.height;
	
	ctx.clearRect(0, 0, w, h);
	ctx.strokeStyle = color;
	ctx.lineWidth = 2;
	ctx.beginPath();
	
	const maxValue = Math.max(...data);
	const step = w / (data.length - 1);
	
	data.forEach((v, i) => {
		const x = i * step;
		const y = h - (v / maxValue) * (h - 20);
		if (i === 0) ctx.moveTo(x, y);
		else ctx.lineTo(x, y);
	});
	
	ctx.stroke();
	
	if (fill) {
		ctx.lineTo(w, h);
		ctx.lineTo(0, h);
		ctx.closePath();
		ctx.fillStyle = 'rgba(15,98,254,0.10)';
		ctx.fill();
	}
}

function drawPieChart(canvasId, data, colors) {
	const cv = document.getElementById(canvasId);
	if (!cv || !data || data.length === 0) return;
	
	const ctx = cv.getContext('2d');
	const w = cv.width = cv.clientWidth;
	const h = cv.height;
	const centerX = w / 2;
	const centerY = h / 2;
	const radius = Math.min(w, h) / 2 - 20;
	
	ctx.clearRect(0, 0, w, h);
	
	const total = data.reduce((sum, item) => sum + item.value, 0);
	let currentAngle = -Math.PI / 2;
	
	data.forEach((item, index) => {
		const sliceAngle = (item.value / total) * 2 * Math.PI;
		
		ctx.beginPath();
		ctx.moveTo(centerX, centerY);
		ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
		ctx.closePath();
		ctx.fillStyle = colors[index % colors.length];
		ctx.fill();
		
		// Add labels
		const labelAngle = currentAngle + sliceAngle / 2;
		const labelX = centerX + Math.cos(labelAngle) * (radius * 0.7);
		const labelY = centerY + Math.sin(labelAngle) * (radius * 0.7);
		
		ctx.fillStyle = '#fff';
		ctx.font = '12px Inter';
		ctx.textAlign = 'center';
		ctx.fillText(item.label, labelX, labelY);
		
		currentAngle += sliceAngle;
	});
}

// Render all charts
function renderCharts() {
	if (!dashboardData) return;
	
	// Chart A: Responses over time
	const responsesData = dashboardData.responsesOverTime || [0, 5, 12, 8, 15, 20, 18];
	drawLine('chartA', responsesData, '#3b82f6', true);
	
	// Chart B: Survey types distribution
	const surveyTypes = dashboardData.surveyTypes || [
		{ label: 'Surveys', value: 60 },
		{ label: 'Quizzes', value: 40 }
	];
	const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
	drawPieChart('chartB', surveyTypes, colors);
}

// Render recent activity
function renderRecentActivity() {
	const container = document.getElementById('recentActivity');
	if (!container) return;
	
	const activities = dashboardData?.recentActivity || [
		{ type: 'response', message: 'New response to "Customer Satisfaction Survey"', time: '2 minutes ago' },
		{ type: 'survey', message: 'Survey "Employee Feedback" was published', time: '1 hour ago' },
		{ type: 'response', message: 'Quiz "Product Knowledge Test" completed', time: '3 hours ago' }
	];
	
	container.innerHTML = activities.map(activity => `
		<div style="display: flex; align-items: center; margin-bottom: 12px; padding: 8px; border-radius: 6px; background: #f8fafc;">
			<div style="width: 8px; height: 8px; border-radius: 50%; background: ${activity.type === 'response' ? '#10b981' : '#3b82f6'}; margin-right: 12px;"></div>
			<div style="flex: 1;">
				<div style="font-size: 14px; color: #374151;">${activity.message}</div>
				<div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${activity.time}</div>
			</div>
		</div>
	`).join('');
}

// Render top performing surveys
function renderTopSurveys() {
	const container = document.getElementById('topSurveys');
	if (!container) return;
	
	const surveys = dashboardData?.topSurveys || [
		{ title: 'Customer Satisfaction Survey', responses: 45, rate: '89%' },
		{ title: 'Employee Feedback Form', responses: 32, rate: '76%' },
		{ title: 'Product Knowledge Quiz', responses: 28, rate: '94%' }
	];
	
	container.innerHTML = surveys.map((survey, index) => `
		<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding: 12px; border-radius: 6px; background: #f8fafc;">
			<div>
				<div style="font-size: 14px; font-weight: 500; color: #374151;">${survey.title}</div>
				<div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${survey.responses} responses</div>
			</div>
			<div style="text-align: right;">
				<div style="font-size: 16px; font-weight: 600; color: #10b981;">${survey.rate}</div>
				<div style="font-size: 12px; color: #6b7280;">completion</div>
			</div>
		</div>
	`).join('');
}

