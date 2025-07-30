<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<link rel="icon" type="..image/png" href="../images/cutie_2.0.png">
<link rel="stylesheet" href="../css/styles.css?v=1.0">
<link rel="stylesheet" href="../assets/css/all.min.css">
<style>
.aibutton {
  position: fixed;
  top: 0%;
  left: 95%;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color:rgb(217, 210, 255);
  color: white;
  font-size: 24px;
  border: 2px solid #ccc;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  z-index: 9999;
  user-select: none;
  transition: top 0.25s ease, left 0.25s ease;
}
.aibutton img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  pointer-events: none;    /* Prevent mouse events on the image */
  user-drag: none;         /* Disable drag on Safari */
  -webkit-user-drag: none; /* Disable drag on Chrome and other WebKit browsers */
}


.aibutton:hover {
  background-color:rgb(255, 255, 255);
  transform: scale(1.1);
}


/* .aibutton.active {
  right: 320px; 
} */
</style>
<div class="sidebar-container">
<div class="sidebar">

    <img src="../images/SYSTEM-LOGOv4.png" alt="Logo" class="logo">
    <h1 class="admin-title">ADMIN PANEL</h1>
    <ul class="menu">
        <li class="dashboard-button"><a href="dashboard.php"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
        <li class="applicants-button"><a href="applicants.php"><i class="fa-solid fa-user-plus"></i>Applicants Management</a></li>
        <li class="employee-button"><a href="employee.php"><i class="fa-solid fa-users"></i>Employee Records</a></li>
        <!-- <li class="password-button"><a href=""><i class="fa-solid fa-calendar-check"></i>Attendance Management</a></li> -->
        <li class="performance-button"><a href="performance_appraisal.php"><i class="fa-solid fa-chart-line"></i>Performance Appraisal</a></li>
        <li class="document-button"><a href="document_mgmt.php"><i class="fa-solid fa-file-lines"></i>Document Management</a></li>
        <li class="subject-button"><a href=""><i class="fa-solid fa-chart-pie"></i>Reports & Analytics</a></li>
        <li class="settings-button"><a href="settings.php"><i class="fa-solid fa-gear"></i>Settings</a></li>
        <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Logout </a></li>
       
 
    </ul>


    

</div>
</div>

<button class="aibutton inactive" onclick="toggleAISidebar()">
  <img src="../images/cutie_2.0.png" alt="AI" />
</button>

<div class="box-footer">
    <span>2025 | Copyright Team Quiet</span>
    <span>Human Resources Information Management System</span>
</div>
<?php include 'aisidebar.php'; ?>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("aiSidebar").classList.add("collapsed");
    document.querySelector(".aibutton").classList.add("inactive");
  });

  function makeDraggable(el) {
    let offsetX = 0, offsetY = 0, isDown = false, dragged = false;
    let isDraggable = true;

    el.addEventListener('mousedown', function(e) {
      if (!isDraggable) return;
      isDown = true;
      dragged = false;
      offsetX = e.clientX - el.offsetLeft;
      offsetY = e.clientY - el.offsetTop;
      el.style.cursor = 'grabbing';
      el.style.transition = 'none';
    });

    document.addEventListener('mouseup', function(e) {
      if (!isDown) return;
      isDown = false;
      el.style.cursor = 'pointer';
      el.style.transition = 'top 0.25s ease, left 0.25s ease';
    });

    document.addEventListener('mousemove', function(e) {
      if (!isDown || !isDraggable) return;
      dragged = true; // mark that a drag happened
      el.style.left = `${e.clientX - offsetX}px`;
      el.style.top = `${e.clientY - offsetY}px`;
    });

    // return both control and "wasDragged" checker
    return {
      enable: () => { isDraggable = true; el.style.cursor = 'pointer'; },
      disable: () => { isDraggable = false; el.style.cursor = 'default'; },
      wasDragged: () => dragged
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    const aiButton = document.querySelector('.aibutton');
    const aiSidebar = document.getElementById('aiSidebar');

    const dragController = makeDraggable(aiButton);

    aiButton.addEventListener('click', (e) => {
      if (dragController.wasDragged()) {
        // Prevent click toggle if we just dragged
        e.stopImmediatePropagation();
        return;
      }

      const isOpen = aiSidebar.classList.toggle('active');
      aiButton.classList.toggle('active');
      aiButton.classList.toggle('inactive');

      if (isOpen) {
        aiButton.style.top = '0%';
        aiButton.style.left = 'calc(97.5% - 340px)';
        dragController.disable();
      } else {
        dragController.enable();
      }
    });
  });
</script>
