<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>HRIMS Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 20px;
    }
    .dashboard-button {
      background-color: #00124d;
      border-left: 4px solid #ffffff;
    }
    .header {
      font-size: 24px;
      font-weight: bold;
      margin-left: 250px;
      padding-bottom: 10px;
    }
    .content {
      margin-left: 250px;
      padding: 20px;
    }
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .summary-card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      text-align: center;
    }
    .summary-title {
      font-size: 14px;
      color: #777;
    }
    .summary-value {
      font-size: 28px;
      font-weight: bold;
      margin-top: 5px;
    }
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 20px;
    }
    .card {
      background: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    h2 {
      font-size: 18px;
      margin-bottom: 12px;
    }
    canvas {
      width: 100% !important;
      height: auto !important;
    }
    .birthday-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 10px;
      width: 160px;
      text-align: center;
      background-color: #fafafa;
    }
    .birthday-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 8px;
    }
    .birthday-name {
      font-weight: bold;
      font-size: 14px;
    }
    .birthday-date {
      font-size: 12px;
      color: #666;
    }
    #yearlyChart {
  width: 100% !important;
  height: 400px !important;
}
.wide-chart {
  grid-column: span 2;
}

@media screen and (max-width: 768px) {
  .wide-chart {
    grid-column: span 1; /* Make it stack on smaller screens */
  }
}
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="header">Employee Dashboard</div>

<div class="content">
<div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
  <div class="card" style="flex: 1; min-width: 200px;">
    <h2><i class="fas fa-users" style="font-size: 30px;"></i> Total Employees</h2>
    <div id="totalEmployees" style="font-size: 24px; font-weight: bold; text-align:center">0</div>
  </div>

  <div class="card" style="flex: 1; min-width: 200px;">
    <h2><i class="fas fa-user-plus" style="font-size: 30px;"></i> Newly Hired This Month</h2>
    <div id="newlyHired" style="font-size: 24px; font-weight: bold; text-align:center">0</div>
  </div>

  <div style="flex: 1; min-width: 200px;" class="card">
    <h2>Filter by Department</h2>
    <select id="deptFilter" style="width: 100%; padding: 8px; font-size: 16px;">
      <option value="">All Departments</option>
    </select>
  </div>
</div>


  <div class="dashboard-grid">
      <div class="card wide-chart">
      <h2>Employee Type by Year</h2>
      <canvas id="yearlyChart"></canvas>
    </div>

    <div class="card">
      <h2>Teaching vs Non-Teaching</h2>
      <canvas id="deptChart"></canvas>
    </div>

    <div class="card">
      <h2>Teaching: Full-time vs Part-time</h2>
      <canvas id="teachingTypeChart"></canvas>
    </div>

    <div class="card">
      <h2>🎉 Birthdays This Month</h2>
      <div id="birthdayCards" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
    </div>
  </div>
</div>

<script src="../assets/js/chart.umd.js"></script>
<script>
  fetch('../handlers/get_dashboard_data.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('totalEmployees').textContent = data.totalEmployees || 0;

      const years = Object.keys(data.yearlyStats).sort();
      const employmentTypes = ['full-time', 'part-time', 'permanent'];
      const yearlyDatasets = employmentTypes.map(type => ({
        label: type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()),
        data: years.map(year => data.yearlyStats[year]?.[type] || 0),
        backgroundColor: getColorForType(type)
      }));
      new Chart(document.getElementById('yearlyChart'), {
        type: 'line',
        data: {
          labels: years.length > 0 ? years : ['No Data'],
          datasets: years.length > 0 ? yearlyDatasets.map(dataset => ({
            ...dataset,
            fill: false,
            borderColor: dataset.backgroundColor,
            tension: 0.3
          })) : [{ label: 'No Data', data: [0], fill: false, borderColor: '#ccc' }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Employee Types by Year' }
          },
          scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Number of Employees' } },
            x: { title: { display: true, text: 'Year' } }
          }
        }
      });

      new Chart(document.getElementById('deptChart'), {
        type: 'pie',
        data: {
          labels: ['Teaching', 'Non-Teaching'],
          datasets: [{
            data: [data.teachingStats.teaching, data.teachingStats.non_teaching].some(n => n > 0) ? [data.teachingStats.teaching, data.teachingStats.non_teaching] : [1, 0],
            backgroundColor: ['#5700FF', '#FFF500']
          }]
        }
      });

      const teachTypeData = [data.teachingType.full_time, data.teachingType.part_time];
      const hasData = teachTypeData.some(n => n > 0);

      new Chart(document.getElementById('teachingTypeChart'), {
        type: 'doughnut',
        data: {
          labels: ['Full-time', 'Part-time'],
          datasets: [{
            data: hasData ? teachTypeData : [1, 1],
            backgroundColor: hasData ? ['#3498db', '#f39c12'] : ['#e0e0e0', '#e0e0e0'],
            borderColor: '#ccc',
            borderWidth: 1
          }]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: hasData ? 'Teaching: Full-time vs Part-time' : 'No data available'
            },
            tooltip: { enabled: hasData }
          }
        }
      });
    })
    .catch(err => {
      console.error('Dashboard error:', err);
      alert("Failed to load dashboard data. Please check the console.");
    });

  function getColorForType(type) {
    switch (type) {
      case 'full-time': return '#4CAF50';
      case 'part-time': return '#2196F3';
      case 'permanent': return '#FFC107';
      case 'unknown': return '#E0E0E0';
      default: return '#9E9E9E';
    }
  }

  fetch('../handlers/get_birthdays.php')
    .then(res => res.json())
    .then(employees => {
      const container = document.getElementById('birthdayCards');
      if (employees.length === 0) {
        container.innerHTML = "<p>No birthdays this month.</p>";
      } else {
        employees.forEach(emp => {
          const card = document.createElement('div');
          card.className = 'birthday-card';
          const photoUrl = emp.photo ? `../handlers/get_image.php?id=${emp.photo}` : '../image/placeholder.png';
          card.innerHTML = `
            <img src="${photoUrl}" alt="Photo of ${emp.name}">
            <div class="birthday-name">${emp.name}</div>
            <div class="birthday-date">${new Date(emp.birth_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric' })}</div>
          `;
          container.appendChild(card);
        });
      }
    })
    .catch(err => {
      console.error('Error loading birthday data:', err);
      alert("Failed to load birthday data. Please check the console.");
    });
</script>

</body>
</html>