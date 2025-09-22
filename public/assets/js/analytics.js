// Mock data for analytics
const mockResponses = [
	{
		id: 1,
		name: "أحمد محمد",
		email: "ahmed@example.com",
		gender: "male",
		age: 28,
		education: "bachelor",
		location: "riyadh",
		surveyType: "survey",
		score: 85,
		date: "2024-01-15",
		responses: { q1: "ممتاز", q2: "جيد جداً", q3: "مقبول" }
	},
	{
		id: 2,
		name: "فاطمة علي",
		email: "fatima@example.com",
		gender: "female",
		age: 32,
		education: "master",
		location: "jeddah",
		surveyType: "quiz",
		score: 92,
		date: "2024-01-16",
		responses: { q1: "صحيح", q2: "خطأ", q3: "صحيح" }
	},
	{
		id: 3,
		name: "محمد السعد",
		email: "mohammed@example.com",
		gender: "male",
		age: 24,
		education: "high-school",
		location: "dammam",
		surveyType: "survey",
		score: 78,
		date: "2024-01-17",
		responses: { q1: "جيد", q2: "ممتاز", q3: "جيد جداً" }
	},
	{
		id: 4,
		name: "نورا أحمد",
		email: "nora@example.com",
		gender: "female",
		age: 29,
		education: "phd",
		location: "riyadh",
		surveyType: "quiz",
		score: 88,
		date: "2024-01-18",
		responses: { q1: "صحيح", q2: "صحيح", q3: "خطأ" }
	},
	{
		id: 5,
		name: "خالد العتيبي",
		email: "khalid@example.com",
		gender: "male",
		age: 35,
		education: "bachelor",
		location: "jeddah",
		surveyType: "survey",
		score: 91,
		date: "2024-01-19",
		responses: { q1: "ممتاز", q2: "ممتاز", q3: "جيد جداً" }
	},
	{
		id: 6,
		name: "سارة النجار",
		email: "sara@example.com",
		gender: "female",
		age: 26,
		education: "master",
		location: "riyadh",
		surveyType: "quiz",
		score: 95,
		date: "2024-01-20",
		responses: { q1: "صحيح", q2: "صحيح", q3: "صحيح" }
	}
];

let filteredData = [...mockResponses];

// Initialize analytics
document.addEventListener('DOMContentLoaded', () => {
	renderCharts();
	renderTable();
	setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
	document.getElementById('applyFilters').addEventListener('click', applyFilters);
	document.getElementById('clearFilters').addEventListener('click', clearFilters);
	document.getElementById('exportData').addEventListener('click', exportData);
	
	// Auto-apply filters on change
	const filterInputs = document.querySelectorAll('#genderFilter, #ageFilter, #educationFilter, #locationFilter, #surveyTypeFilter, #dateFrom, #dateTo');
	filterInputs.forEach(input => {
		input.addEventListener('change', applyFilters);
	});
}

// Apply filters
function applyFilters() {
	const gender = document.getElementById('genderFilter').value;
	const age = document.getElementById('ageFilter').value;
	const education = document.getElementById('educationFilter').value;
	const location = document.getElementById('locationFilter').value;
	const surveyType = document.getElementById('surveyTypeFilter').value;
	const dateFrom = document.getElementById('dateFrom').value;
	const dateTo = document.getElementById('dateTo').value;

	filteredData = mockResponses.filter(response => {
		// Gender filter
		if (gender && response.gender !== gender) return false;
		
		// Age filter
		if (age) {
			const [minAge, maxAge] = age.split('-').map(Number);
			if (maxAge) {
				if (response.age < minAge || response.age > maxAge) return false;
			} else {
				if (response.age < minAge) return false;
			}
		}
		
		// Education filter
		if (education && response.education !== education) return false;
		
		// Location filter
		if (location && response.location !== location) return false;
		
		// Survey type filter
		if (surveyType && response.surveyType !== surveyType) return false;
		
		// Date range filter
		if (dateFrom && response.date < dateFrom) return false;
		if (dateTo && response.date > dateTo) return false;
		
		return true;
	});

	renderCharts();
	renderTable();
}

// Clear all filters
function clearFilters() {
	document.getElementById('genderFilter').value = '';
	document.getElementById('ageFilter').value = '';
	document.getElementById('educationFilter').value = '';
	document.getElementById('locationFilter').value = '';
	document.getElementById('surveyTypeFilter').value = '';
	document.getElementById('dateFrom').value = '';
	document.getElementById('dateTo').value = '';
	
	filteredData = [...mockResponses];
	renderCharts();
	renderTable();
}

// Render charts
function renderCharts() {
	renderResponseChart();
	renderDemographicChart();
	renderAgeChart();
	renderEducationChart();
}

// Response distribution chart
function renderResponseChart() {
	const canvas = document.getElementById('responseChart');
	if (!canvas) return;
	
	const ctx = canvas.getContext('2d');
	const w = canvas.width = canvas.clientWidth;
	const h = canvas.height;
	
	ctx.clearRect(0, 0, w, h);
	
	// Calculate score distribution
	const scoreRanges = {
		'0-60': 0,
		'61-70': 0,
		'71-80': 0,
		'81-90': 0,
		'91-100': 0
	};
	
	filteredData.forEach(response => {
		if (response.score <= 60) scoreRanges['0-60']++;
		else if (response.score <= 70) scoreRanges['61-70']++;
		else if (response.score <= 80) scoreRanges['71-80']++;
		else if (response.score <= 90) scoreRanges['81-90']++;
		else scoreRanges['91-100']++;
	});
	
	// Draw bar chart
	const maxCount = Math.max(...Object.values(scoreRanges));
	const barWidth = w / 5;
	const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#16a34a'];
	
	Object.entries(scoreRanges).forEach(([range, count], index) => {
		const barHeight = (count / maxCount) * (h - 40);
		const x = index * barWidth + 10;
		const y = h - barHeight - 20;
		
		ctx.fillStyle = colors[index];
		ctx.fillRect(x, y, barWidth - 20, barHeight);
		
		// Draw labels
		ctx.fillStyle = '#374151';
		ctx.font = '12px Cairo';
		ctx.textAlign = 'center';
		ctx.fillText(range, x + (barWidth - 20) / 2, h - 5);
		ctx.fillText(count.toString(), x + (barWidth - 20) / 2, y - 5);
	});
}

// Demographic breakdown chart
function renderDemographicChart() {
	const canvas = document.getElementById('demographicChart');
	if (!canvas) return;
	
	const ctx = canvas.getContext('2d');
	const w = canvas.width = canvas.clientWidth;
	const h = canvas.height;
	
	ctx.clearRect(0, 0, w, h);
	
	// Calculate gender distribution
	const genderCount = { male: 0, female: 0, other: 0 };
	filteredData.forEach(response => {
		genderCount[response.gender]++;
	});
	
	// Draw pie chart
	const total = filteredData.length;
	let currentAngle = 0;
	const colors = ['#3b82f6', '#ec4899', '#10b981'];
	const labels = ['Male', 'Female', 'Other'];
	
	Object.entries(genderCount).forEach(([gender, count], index) => {
		if (count === 0) return;
		
		const sliceAngle = (count / total) * 2 * Math.PI;
		
		ctx.beginPath();
		ctx.moveTo(w/2, h/2);
		ctx.arc(w/2, h/2, Math.min(w, h)/2 - 20, currentAngle, currentAngle + sliceAngle);
		ctx.closePath();
		ctx.fillStyle = colors[index];
		ctx.fill();
		
		// Draw label
		const labelAngle = currentAngle + sliceAngle / 2;
		const labelX = w/2 + Math.cos(labelAngle) * (Math.min(w, h)/2 + 10);
		const labelY = h/2 + Math.sin(labelAngle) * (Math.min(w, h)/2 + 10);
		
		ctx.fillStyle = '#374151';
		ctx.font = '12px Cairo';
		ctx.textAlign = 'center';
		ctx.fillText(`${labels[index]}: ${count}`, labelX, labelY);
		
		currentAngle += sliceAngle;
	});
}

// Age distribution chart
function renderAgeChart() {
	const canvas = document.getElementById('ageChart');
	if (!canvas) return;
	
	const ctx = canvas.getContext('2d');
	const w = canvas.width = canvas.clientWidth;
	const h = canvas.height;
	
	ctx.clearRect(0, 0, w, h);
	
	// Calculate age distribution
	const ageGroups = {
		'18-25': 0,
		'26-35': 0,
		'36-45': 0,
		'46-55': 0,
		'55+': 0
	};
	
	filteredData.forEach(response => {
		if (response.age <= 25) ageGroups['18-25']++;
		else if (response.age <= 35) ageGroups['26-35']++;
		else if (response.age <= 45) ageGroups['36-45']++;
		else if (response.age <= 55) ageGroups['46-55']++;
		else ageGroups['55+']++;
	});
	
	// Draw line chart
	const maxCount = Math.max(...Object.values(ageGroups));
	const stepX = w / 5;
	const colors = ['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];
	
	ctx.strokeStyle = '#3b82f6';
	ctx.lineWidth = 3;
	ctx.beginPath();
	
	Object.entries(ageGroups).forEach(([group, count], index) => {
		const x = index * stepX + stepX/2;
		const y = h - (count / maxCount) * (h - 40) - 20;
		
		if (index === 0) {
			ctx.moveTo(x, y);
		} else {
			ctx.lineTo(x, y);
		}
		
		// Draw points
		ctx.fillStyle = colors[index];
		ctx.beginPath();
		ctx.arc(x, y, 4, 0, 2 * Math.PI);
		ctx.fill();
		
		// Draw labels
		ctx.fillStyle = '#374151';
		ctx.font = '10px Cairo';
		ctx.textAlign = 'center';
		ctx.fillText(group, x, h - 5);
		ctx.fillText(count.toString(), x, y - 10);
	});
	
	ctx.stroke();
}

// Education level chart
function renderEducationChart() {
	const canvas = document.getElementById('educationChart');
	if (!canvas) return;
	
	const ctx = canvas.getContext('2d');
	const w = canvas.width = canvas.clientWidth;
	const h = canvas.height;
	
	ctx.clearRect(0, 0, w, h);
	
	// Calculate education distribution
	const educationCount = {
		'high-school': 0,
		'bachelor': 0,
		'master': 0,
		'phd': 0
	};
	
	filteredData.forEach(response => {
		educationCount[response.education]++;
	});
	
	// Draw horizontal bar chart
	const maxCount = Math.max(...Object.values(educationCount));
	const barHeight = (h - 60) / 4;
	const labels = ['High School', 'Bachelor', 'Master', 'PhD'];
	const colors = ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6'];
	
	Object.entries(educationCount).forEach(([level, count], index) => {
		const barWidth = (count / maxCount) * (w - 100);
		const y = 20 + index * (barHeight + 10);
		
		ctx.fillStyle = colors[index];
		ctx.fillRect(80, y, barWidth, barHeight);
		
		// Draw labels
		ctx.fillStyle = '#374151';
		ctx.font = '12px Cairo';
		ctx.textAlign = 'right';
		ctx.fillText(labels[index], 75, y + barHeight/2 + 4);
		ctx.textAlign = 'left';
		ctx.fillText(count.toString(), barWidth + 85, y + barHeight/2 + 4);
	});
}

// Render data table
function renderTable() {
	const tbody = document.getElementById('tableBody');
	if (!tbody) return;
	
	tbody.innerHTML = '';
	
	filteredData.forEach(response => {
		const row = document.createElement('tr');
		
		const ageGroup = response.age <= 25 ? '18-25' :
						response.age <= 35 ? '26-35' :
						response.age <= 45 ? '36-45' :
						response.age <= 55 ? '46-55' : '55+';
		
		const educationLabels = {
			'high-school': 'High School',
			'bachelor': 'Bachelor',
			'master': 'Master',
			'phd': 'PhD'
		};
		
		const locationLabels = {
			'riyadh': 'Riyadh',
			'jeddah': 'Jeddah',
			'dammam': 'Dammam',
			'other': 'Other'
		};
		
		row.innerHTML = `
			<td>${response.name}</td>
			<td>${response.email}</td>
			<td>${response.gender === 'male' ? 'Male' : response.gender === 'female' ? 'Female' : 'Other'}</td>
			<td>${ageGroup}</td>
			<td>${educationLabels[response.education]}</td>
			<td>${locationLabels[response.location]}</td>
			<td>${response.surveyType === 'survey' ? 'Survey' : 'Quiz'}</td>
			<td>${response.score}</td>
			<td>${response.date}</td>
			<td>
				<button class="icon-btn" onclick="viewResponse(${response.id})" title="View Details">👁️</button>
			</td>
		`;
		
		tbody.appendChild(row);
	});
}

// Export data
function exportData() {
	const csvContent = generateCSV();
	const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
	const link = document.createElement('a');
	const url = URL.createObjectURL(blob);
	link.setAttribute('href', url);
	link.setAttribute('download', 'analytics_data.csv');
	link.style.visibility = 'hidden';
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}

// Generate CSV content
function generateCSV() {
	const headers = ['Name', 'Email', 'Gender', 'Age', 'Education', 'Location', 'Survey Type', 'Score', 'Date'];
	const csvRows = [headers.join(',')];
	
	filteredData.forEach(response => {
		const ageGroup = response.age <= 25 ? '18-25' :
						response.age <= 35 ? '26-35' :
						response.age <= 45 ? '36-45' :
						response.age <= 55 ? '46-55' : '55+';
		
		const educationLabels = {
			'high-school': 'High School',
			'bachelor': 'Bachelor',
			'master': 'Master',
			'phd': 'PhD'
		};
		
		const locationLabels = {
			'riyadh': 'Riyadh',
			'jeddah': 'Jeddah',
			'dammam': 'Dammam',
			'other': 'Other'
		};
		
		const row = [
			response.name,
			response.email,
			response.gender === 'male' ? 'Male' : response.gender === 'female' ? 'Female' : 'Other',
			ageGroup,
			educationLabels[response.education],
			locationLabels[response.location],
			response.surveyType === 'survey' ? 'Survey' : 'Quiz',
			response.score,
			response.date
		];
		csvRows.push(row.join(','));
	});
	
	return csvRows.join('\n');
}

// View response details
function viewResponse(id) {
	const response = mockResponses.find(r => r.id === id);
	if (response) {
		if (typeof window.toast !== 'undefined') {
			window.toast.info(`Response Details:\nName: ${response.name}\nEmail: ${response.email}\nScore: ${response.score}`, 5000);
		} else {
			alert(`Response Details:\nName: ${response.name}\nEmail: ${response.email}\nScore: ${response.score}\nResponses: ${JSON.stringify(response.responses)}`);
		}
	}
}
