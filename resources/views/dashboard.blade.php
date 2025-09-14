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
				<div class="card stat"><div class="icon">👥</div><div><div class="num" id="activeUsers">-</div><div class="lbl">Active Users</div></div></div>
				<div class="card stat"><div class="icon">📈</div><div><div class="num" id="responseRate">-%</div><div class="lbl">Response Rate</div></div></div>
			</section>
			<section class="grid">
				<div class="card">
					<div class="card-title">Survey Responses Over Time</div>
					<canvas id="chartA" height="180"></canvas>
				</div>
				<div class="card">
					<div class="card-title">Survey Types Distribution</div>
					<canvas id="chartB" height="180"></canvas>
				</div>
				<div class="card">
					<div class="card-title">Recent Activity</div>
					<div id="recentActivity" style="padding: 20px;">
						<div class="loading">Loading recent activity...</div>
					</div>
				</div>
				<div class="card">
					<div class="card-title">Top Performing Surveys</div>
					<div id="topSurveys" style="padding: 20px;">
						<div class="loading">Loading survey data...</div>
					</div>
				</div>
			</section>
		</main>
	</div>

	<script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>
</html>

