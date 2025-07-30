<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>We Are Hiring</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Fredoka', sans-serif;
    }

    body, html {
      height: 100%;
      overflow: hidden;
      background-image: url('../images/exact_school_front.png');
      background-size: cover;
      background-position: center;
    }

    .container {
      height: 100vh;
      position: relative;
      backdrop-filter: blur(3px);
      align-items: center;
    }

    .overlay {
      background-color: rgba(255, 255, 255, 0.37);
      border: 2px solid white;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      padding: 40px 30px;
      border-radius: 20px;
      width: auto;
      height: auto;
    }

    .logo {
      width: 130px;
      height: 130px;
      margin-bottom: 10px;
      top: 0px;
      left: 46%;
      position: relative;
    }

    h2 {
      color: white;
      font-size: 40px;
      margin-bottom: 10px;
      text-shadow: 3px 5px 5px rgba(79, 79, 79, 0.66);
    }

    .highlight {
      font-size: 75px;
      font-weight: 700;
      color: rgb(0, 98, 210);
      letter-spacing: 15px;
      font-family: 'Times New Roman', Times, serif;
      text-shadow: 3px 5px 5px rgba(53, 86, 221, 0.66);
    }

    p {
      color: white;
      font-size: 35px;
      margin-bottom: 30px;
      text-shadow: 3px 5px 5px rgba(70, 70, 70, 0.66);
    }

    .search-box {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .search-box input {
      padding: 10px;
      width: 200px;
      border: 2px solid #003f88;
      border-radius: 30px 0 0 30px;
      outline: none;
    }

    .search-box button {
      background-color: #003f88;
      border: none;
      color: white;
      padding: 10px 16px;
      border-radius: 0 30px 30px 0;
      cursor: pointer;
    }

    .apply-btn {
      background-color: #003fdd;
      color: white;
      padding: 20px 65px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 30px;
    }

    .dots {
      display: flex;
      justify-content: center;
      margin-top: 30px;
    }

    .dots span {
      height: 12px;
      width: 12px;
      background-color: white;
      border-radius: 50%;
      margin: 0 5px;
      opacity: 0.6;
    }

    .dots span:first-child {
      opacity: 1;
    }

    .carousel-indicators {
      display: flex;
      gap: 10px;
      position: absolute;
    }

    .carousel-indicators span {
      height: 20px;
      width: 20px;
      border: 2px solid white;
      border-radius: 50%;
      background-color: transparent;
      opacity: 0.8;
    }

    .carousel-indicators span.active {
      background-color: white;
    }

    .top-left { top: 20px; left: 20px; }
    .bottom-left { bottom: 20px; left: 20px; }
    .top-right { top: 20px; right: 20px; }
    .bottom-right { bottom: 20px; right: 20px; }

    #job-popup {
      display: none;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid #ccc;
      border-radius: 10px;
      padding: 15px;
      position: absolute;
      top: 65%;
      left: 50%;
      transform: translate(-50%, 0);
      width: 80%;
      max-width: 500px;
      z-index: 999;
      color: black;
    }

    #job-popup ul {
      list-style: none;
      padding: 0;
    }

    #job-popup ul li {
      padding: 5px 0;
      border-bottom: 1px solid #ddd;
    }
    .close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: none;
  border: none;
  font-size: 24px;
  font-weight: bold;
  color: #003f88;
  cursor: pointer;
}
.job-table {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.job-row {
  display: flex;
  justify-content: space-between;
  padding: 6px 10px;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 5px;
  font-size: 16px;
}

.job-row.header {
  font-weight: bold;
  background: rgba(0, 63, 136, 0.8);
  color: white;
}

  </style>
</head>
<body>
  <div class="container">
    <img src="../images/exact logo.png" alt="Logo" class="logo" />

    <div class="overlay">
      <h2>We Are</h2>
      <h1><span class="highlight">HIRING</span></h1>
      <p>Start your journey with us today!</p>

      <div class="search-box">
        <input type="text" placeholder="Job Position Available" readonly />
        <button onclick="fetchJobs()"><span>&#128269;</span></button>
      </div>

<div id="job-popup">
      <button class="close-btn" onclick="closePopup()">×</button>
  <strong>Available Job Positions:</strong>
  <div class="job-table">
    <div class="job-row header">
      <span>Position</span>
      <span>Available Slots</span>
    </div>
    <div id="job-list"></div>
  </div>
</div>

      <form action="application_form.php" method="POST">
        <button class="apply-btn">Apply Now!</button>
      </form>

      <div class="dots"></div>
    </div>
  </div>

  <div class="carousel-indicators top-left"><span></span><span></span><span></span><span></span></div>
  <div class="carousel-indicators bottom-left"><span></span><span></span><span></span><span></span></div>
  <div class="carousel-indicators top-right"><span></span><span></span><span></span><span></span></div>
  <div class="carousel-indicators bottom-right"><span></span><span></span><span></span><span></span></div>

  <script>
   function fetchJobs() {
  fetch('../handlers/get_jobs.php')
    .then(response => response.json())
    .then(data => {
      const popup = document.getElementById('job-popup');
      const list = document.getElementById('job-list');
      list.innerHTML = '';

      if (data.length === 0) {
        list.innerHTML = '<div class="job-row"><span>No jobs available</span><span>-</span></div>';
      } else {
        data.forEach(job => {
          const row = document.createElement('div');
          row.className = 'job-row';
          row.innerHTML = `
            <span>${job.position_title}</span>
            <span>${job.available_slots ?? '-'}</span>
          `;
          list.appendChild(row);
        });
      }

      popup.style.display = 'block';
    })
    .catch(error => {
      alert('Error fetching jobs.');
      console.error(error);
    });
}

    function closePopup() {
  document.getElementById('job-popup').style.display = 'none';
}

  </script>
</body>
</html>
