<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<link rel="icon" type="..image/png" href="../images/cutie_2.0.png">
<link rel="stylesheet" href="../css/styles.css?v=1.1">
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
  -webkit-user-drag: none; /* Disable drag on Chrome and other WebKit browsers */
}


.aibutton:hover {
  background-color:rgb(255, 255, 255);
  transform: scale(1.1);
}

/* Sidebar Toggle Styles */
.sidebar-header {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px 0;
}

.sidebar-toggle {
  position: absolute;
  top: 20px;
  right: 15px;
  background: none;
  border: none;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background-color 0.3s ease;
  z-index: 10;
  display: block;
}

.sidebar-toggle:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

/* Collapsed sidebar styles */
.sidebar-container.collapsed {
  width: 60px;
  transition: width 0.3s ease;
}

/* Disable transition during initial load to prevent glitch */
.sidebar-container.no-transition {
  transition: none !important;
}

.sidebar-container.collapsed .admin-title {
  display: none;
}

.sidebar-container.collapsed .menu li a {
  justify-content: center;
  padding: 10px 0;
}

.sidebar-container.collapsed .menu li a span {
  display: none;
}

.sidebar-container.collapsed .menu li a i {
  margin-right: 0;
  font-size: 20px;
}

.sidebar-container.collapsed .logo {
  width: 40px;
  height: 40px;
  margin-top: 40px;
}

/* .aibutton.active {
  right: 320px; 
} */
</style>
<div class="sidebar-container" id="mainSidebar">
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../images/SYSTEM-LOGOv4.png" alt="Logo" class="logo">
        <h1 class="admin-title">ADMIN PANEL</h1>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <ul class="menu">
        <li class="dashboard-button"><a href="dashboard.php"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a></li>
        <li class="applicants-button"><a href="applicants.php"><i class="fa-solid fa-user-plus"></i><span>Applicants Management</span></a></li>
        <li class="employee-button"><a href="employee.php"><i class="fa-solid fa-users"></i><span>Employee Records</span></a></li>
        <!-- <li class="password-button"><a href=""><i class="fa-solid fa-calendar-check"></i><span>Attendance Management</span></a></li> -->
        <li class="performance-button"><a href="performance_appraisal.php"><i class="fa-solid fa-chart-line"></i><span>Performance Appraisal</span></a></li>
        <!-- <li class="document-button"><a href="document_mgmt.php"><i class="fa-solid fa-file-lines"></i><span>Document Management</span></a></li> -->
        <li class="subject-button"><a href=""><i class="fa-solid fa-chart-pie"></i><span>Reports & Analytics</span></a></li>
        <li class="settings-button"><a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a></li>
        <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
       

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
  // Apply saved sidebar state immediately to prevent glitch
  (function() {
    const savedSidebarState = localStorage.getItem('sidebarCollapsed');
    const isMobile = window.innerWidth <= 768;
    
    if (!isMobile && savedSidebarState === 'true') {
      // Add no-transition class to prevent animation during initial load
      document.documentElement.style.setProperty('--sidebar-transition', 'none');
      
      // Apply collapsed state immediately when DOM is ready
      document.addEventListener('DOMContentLoaded', function() {
        const mainSidebar = document.getElementById('mainSidebar');
        const content = document.querySelector('.content');
        const header = document.querySelector('.header');
        const boxFooter = document.querySelector('.box-footer');
        
        if (mainSidebar) {
          mainSidebar.classList.add('collapsed', 'no-transition');
          if (content) content.style.marginLeft = '60px';
          if (header) header.style.marginLeft = '60px';
          if (boxFooter) boxFooter.style.marginLeft = '60px';
          
          // Re-enable transitions after a short delay
          setTimeout(() => {
            mainSidebar.classList.remove('no-transition');
          }, 100);
        }
      });
    }
  })();

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
    const mainSidebar = document.getElementById('mainSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const content = document.querySelector('.content');
    const header = document.querySelector('.header');
    const boxFooter = document.querySelector('.box-footer');

    // Set initial box-footer margin and width based on sidebar state
    const isMobile = window.innerWidth <= 768;
    const isCollapsed = mainSidebar.classList.contains('collapsed') || localStorage.getItem('sidebarCollapsed') === 'true';
    if (isMobile) {
      if (boxFooter) {
        boxFooter.style.marginLeft = '0';
        boxFooter.style.width = '100%';
      }
      if (content) content.style.marginLeft = '0';
      if (header) header.style.marginLeft = '0';
    } else if (isCollapsed) {
      if (boxFooter) {
        boxFooter.style.marginLeft = '60px';
        boxFooter.style.width = '100%';
      }
      if (content) content.style.marginLeft = '60px';
      if (header) header.style.marginLeft = '60px';
    } else {
      if (boxFooter) {
        boxFooter.style.marginLeft = '248px';
        boxFooter.style.width = 'calc(100% - 250px)';
      }
      if (content) content.style.marginLeft = '247px';
      if (header) header.style.marginLeft = '245px';
    }

    const dragController = makeDraggable(aiButton);

    // Main sidebar toggle functionality
    sidebarToggle.addEventListener('click', function() {
      const isMobile = window.innerWidth <= 768;
      if (isMobile) {
        mainSidebar.classList.toggle('mobile-open');
      } else {
        mainSidebar.classList.add('no-transition');
        mainSidebar.classList.toggle('collapsed');
        const isCollapsed = mainSidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        if (mainSidebar.classList.contains('collapsed')) {
          content.style.marginLeft = '60px';
          if (header) header.style.marginLeft = '60px';
          if (boxFooter) {
            boxFooter.style.marginLeft = '60px';
            boxFooter.style.width = '100%';
          }
        } else {
          content.style.marginLeft = '247px';
          if (header) header.style.marginLeft = '245px';
          if (boxFooter) {
            boxFooter.style.marginLeft = '248px';
            boxFooter.style.width = 'calc(100% - 250px)';
          }
        }
        setTimeout(() => {
          mainSidebar.classList.remove('no-transition');
        }, 50);
        
        // Adjust AI button position if AI sidebar is open
        if (aiSidebar.classList.contains('active')) {
          const isMainCollapsed = mainSidebar.classList.contains('collapsed');
          if (isMainCollapsed) {
            aiButton.style.left = 'calc(100% - 340px)';
          } else {
            aiButton.style.left = 'calc(97.5% - 340px)';
          }
        }
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      const isMobile = window.innerWidth <= 768;
      
      if (isMobile) {
        // On mobile, ensure sidebar is hidden by default
        mainSidebar.classList.remove('collapsed');
        mainSidebar.classList.remove('mobile-open');
        content.style.marginLeft = '0';
        if (header) header.style.marginLeft = '0';
        if (boxFooter) {
          boxFooter.style.marginLeft = '0';
          boxFooter.style.width = '100%';
        }
      } else {
        // On desktop, restore normal behavior or saved state
        mainSidebar.classList.remove('mobile-open');
        const savedSidebarState = localStorage.getItem('sidebarCollapsed');
        
        if (savedSidebarState === 'true') {
          mainSidebar.classList.add('collapsed');
          content.style.marginLeft = '60px';
          if (header) header.style.marginLeft = '60px';
          if (boxFooter) {
            boxFooter.style.marginLeft = '60px';
            boxFooter.style.width = '100%';
          }
        } else {
          mainSidebar.classList.remove('collapsed');
          content.style.marginLeft = '247px';
          if (header) header.style.marginLeft = '245px';
          if (boxFooter) {
            boxFooter.style.marginLeft = '248px';
            boxFooter.style.width = 'calc(100% - 250px)';
          }
        }
      }
    });

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
        // Adjust AI button position based on main sidebar state
        const isMainCollapsed = mainSidebar.classList.contains('collapsed');
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
          aiButton.style.left = 'calc(100% - 340px)';
        } else if (isMainCollapsed) {
          aiButton.style.left = 'calc(100% - 340px)';
        } else {
          aiButton.style.left = 'calc(97.5% - 340px)';
        }
        dragController.disable();
      } else {
        dragController.enable();
      }
    });
  });
</script>
