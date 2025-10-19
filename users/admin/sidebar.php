<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<link rel="icon" type="image/png" href="/hrims/images/cutie_2.0.png">
<link rel="stylesheet" href="/hrims/css/styles.css?v=1.1">
<link rel="stylesheet" href="/hrims/assets/css/all.min.css">
<style>
.aibutton {
  position: fixed;
  bottom: 200px;
  right: 100px;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  color: white;
  font-size: 24px;
  border: none;
  box-shadow: 0 4px 15px rgba(37, 117, 252, 0.4);
  cursor: pointer;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  user-select: none;
}

.aibutton:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 20px rgba(37, 117, 252, 0.6);
}

.aibutton img {
  width: 50px;
  height: 50px;
  pointer-events: none;
  -webkit-user-drag: none;
}

.aibutton .ai-content {
  /* position: relative; */
  width: 150px;
  height: 150px;
}

.aibutton .ai-content img {
  width: 100%;
  height: 100%;
  pointer-events: none;
  -webkit-user-drag: none;
  object-fit: contain;
  position: absolute;
  top: 0;
  left: 0;
}

.aibutton .thinking-gif {
  width: 100%;
  height: 100%;
  pointer-events: none;
  -webkit-user-drag: none;
  object-fit: contain;
  position: absolute;
  top: 0;
  left: 0;
  display: none;
}

.aibutton .hover-gif {
  width: 100%;
  height: 100%;
  pointer-events: none;
  -webkit-user-drag: none;
  object-fit: contain;
  position: absolute;
  top: 0;
  left: 0;
  display: none;
}

.aibutton .ai-content .thinking-gif {
  display: none;
  width: 60px;
  height: 60px;
}

.aibutton:not(.thinking):hover .ai-content img:not(.hover-gif):not(.thinking-gif) {
  display: none;
}

.aibutton:not(.thinking):hover .hover-gif {
  display: block;
}

.aibutton.thinking .ai-content img:not(.thinking-gif) {
  display: none;
}

.aibutton.thinking .ai-content .thinking-gif {
  display: block;
}

/* Notification Styles */
.notification-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
}

.notification-bell {
  position: relative;
  cursor: pointer;
  font-size: 24px;
  color: white;
  background: rgba(255, 255, 255, 0.2);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
}

.notification-bell:hover {
  background: rgba(255, 255, 255, 0.3);
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #ffdd00;
  color: #001a66;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 12px;
  font-weight: bold;
}

.notification-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 300px;
  z-index: 1000;
  display: none;
}

.notification-dropdown.show {
  display: block;
}

.notification-header {
  padding: 15px;
  border-bottom: 1px solid #eee;
  font-weight: 600;
  color: #003366;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
}

.notification-item:hover {
  background-color: #f8f9ff;
}

.notification-item.unread {
  background-color: #eef5ff;
}

.notification-title {
  font-weight: 600;
  color: #003366;
  margin-bottom: 5px;
}

.notification-time {
  font-size: 12px;
  color: #666;
}

.notification-message {
  font-size: 14px;
  color: #444;
  margin: 5px 0;
}

.mark-read {
  font-size: 12px;
  color: #003366;
  text-decoration: underline;
  cursor: pointer;
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

/* Add smooth transition effects */
.sidebar-container {
  transition: width 0.3s ease-in-out;
}

.sidebar {
  transition: all 0.3s ease-in-out;
}

.admin-title {
  transition: opacity 0.2s ease-in-out;
}

.menu li a {
  transition: all 0.3s ease-in-out;
}

.menu li a span {
  transition: opacity 0.2s ease-in-out;
}

.logo {
  transition: all 0.3s ease-in-out;
}

/* Collapsed sidebar styles */
.sidebar-container.collapsed {
  width: 60px;
  transition: width 0.3s ease-in-out;
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

/* Add smooth transitions for content area */
.content, .header, .box-footer {
  transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
}
</style>
<div class="sidebar-container" id="mainSidebar">
<div class="sidebar">
    <div class="sidebar-header">
  <img src="/hrims/images/SYSTEM-LOGOv4.png" alt="Logo" class="logo">
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
        <li class="teaching-demos-button"><a href="view_teaching_demos.php"><i class="fa-solid fa-chalkboard-user"></i><span>Teaching Demos</span></a></li>
        <!-- <li class="document-button"><a href="document_mgmt.php"><i class="fa-solid fa-file-lines"></i><span>Document Management</span></a></li> -->
        <!-- <li class="subject-button"><a href="course_grouping.php"><i class="fa-solid fa-graduation-cap"></i><span>Course Grouping Report</span></a></li> -->
        <li class="settings-button"><a href="settings.php"><i class="fa-solid fa-gear"></i><span>Settings</span></a></li>
  <li><a href="/hrims/index.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
       

    </ul>


    

</div>
</div>

<!-- Notification Bell -->
<div class="notification-container" id="notificationContainer">
  <div class="notification-bell">
    <i class="fas fa-bell"></i>
    <div class="notification-badge" id="notificationBadge">0</div>
  </div>
  <div class="notification-dropdown" id="notificationDropdown">
    <div class="notification-header">
      <span>Notifications</span>
      <span class="mark-read" id="markAllRead">Mark all as read</span>
    </div>
    <div class="notification-list" id="notificationList">
      <!-- Notifications will be populated here -->
    </div>
  </div>
</div>

<button class="aibutton" id="aiToggleButton">
  <div class="ai-content">
    <img src="/hrims/images/PEARL_logo.png" alt="AI" />
    <img class="hover-gif" src="/hrims/images/PEARL_hi.gif" alt="AI Hi" />
    <img class="thinking-gif" src="/hrims/images/PEARL_thinkingv2.gif" alt="AI Thinking" />
  </div>
</button>

<div class="box-footer">
    <span>2025 | Copyright Team Quiet</span>
    <span>Human Resources Information Management System</span>
</div>

<!-- Include the AI chat interface -->
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

  // Function to request notification permission
  function requestNotificationPermission() {
    if ('Notification' in window) {
      if (Notification.permission === 'default') {
        Notification.requestPermission().then(function(permission) {
          console.log('Notification permission:', permission);
        });
      }
    }
  }

  // Function to show system notification
  function showSystemNotification(title, message, demoId, isUrgent = false) {
    // Check if browser supports notifications and permission is granted
    if ('Notification' in window && Notification.permission === 'granted') {
      const options = {
        body: message,
        icon: '/hrims/images/SYSTEM-LOGOv4.png',
        badge: '/hrims/images/SYSTEM-LOGOv4.png',
        tag: 'hrims-notification-' + demoId,
        renotify: true,
        requireInteraction: isUrgent // Keep notification open for urgent messages
      };
      
      const notification = new Notification(title, options);
      
      // Auto-close non-urgent notifications after 5 seconds
      if (!isUrgent) {
        setTimeout(() => {
          notification.close();
        }, 5000);
      }
      
      // Handle click on notification - redirect to start demo page
      notification.addEventListener('click', function() {
        window.focus();
        window.location.href = 'start_teaching_demo.php?id=' + demoId;
        this.close();
      });
    }
  }

  // Function to check for scheduled notifications at specific times
  function checkForScheduledNotifications() {
    // Get current time in HH:MM format
    const now = new Date();
    const currentHours = now.getHours();
    const currentMinutes = now.getMinutes();
    const currentTimeString = `${currentHours.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`;
    
    console.log('Checking notifications at:', currentTimeString);
    
    // Check for notifications
    fetch('../../handlers/admin/get_notifications.php')
      .then(response => response.json())
      .then(data => {
        if (data.success && data.notifications.length > 0) {
          // Update bell badge with total count
          updateNotificationBadge(data.count);
          
          // Process each notification
          data.notifications.forEach(notification => {
            // Check if this is a demo reminder
            if (notification.type === 'demo_reminder' && notification.demo_time) {
              // Check if it's time to show the notification (exact match)
              if (notification.demo_time === currentTimeString) {
                // Only show system notification if we haven't shown it before today
                const notificationKey = `${notification.id}-${currentTimeString}-${now.toDateString()}`;
                const shownNotifications = JSON.parse(localStorage.getItem('shownNotifications') || '[]');
                
                if (!shownNotifications.includes(notificationKey)) {
                  // Add to shown notifications
                  shownNotifications.push(notificationKey);
                  // Keep only the last 20 shown notifications
                  if (shownNotifications.length > 20) {
                    shownNotifications.shift();
                  }
                  localStorage.setItem('shownNotifications', JSON.stringify(shownNotifications));
                  
                  console.log('Showing system notification for demo:', notification.title);
                  
                  // Show system notification
                  showSystemNotification(
                    notification.title, 
                    notification.message, 
                    notification.id,
                    notification.is_urgent || false
                  );
                } else {
                  console.log('Notification already shown today:', notificationKey);
                }
              } else {
                console.log('Not time yet for demo. Current:', currentTimeString, 'Demo time:', notification.demo_time);
              }
            }
          });
        } else {
          // Update bell badge with 0 count
          updateNotificationBadge(0);
        }
      })
      .catch(error => {
        console.error('Error checking scheduled notifications:', error);
      });
  }

  // Function to set up scheduled notification checks
  function setupScheduledNotifications() {
    // Check immediately when page loads
    checkForScheduledNotifications();
    
    // Set up interval to check every minute
    setInterval(checkForScheduledNotifications, 60000); // Check every minute
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Request notification permission when page loads
    requestNotificationPermission();
    
    // Set up scheduled notifications
    setupScheduledNotifications();
    
    // Notification system functionality
    const notificationContainer = document.getElementById('notificationContainer');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const markAllRead = document.getElementById('markAllRead');
    const notificationList = document.getElementById('notificationList');
    
    // Toggle notification dropdown
    notificationContainer.addEventListener('click', function(e) {
      e.stopPropagation();
      notificationDropdown.classList.toggle('show');
      if (notificationDropdown.classList.contains('show')) {
        loadNotifications();
      }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
      notificationDropdown.classList.remove('show');
    });
    
    // Mark all as read
    markAllRead.addEventListener('click', function(e) {
      e.stopPropagation();
      // Send request to mark notifications as read
      fetch('../../handlers/admin/mark_notifications_read.php', {
        method: 'POST'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const unreadItems = document.querySelectorAll('.notification-item.unread');
          unreadItems.forEach(item => {
            item.classList.remove('unread');
          });
          updateNotificationBadge(0);
        }
      })
      .catch(error => {
        console.error('Error marking notifications as read:', error);
        // Fallback to client-side update
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => {
          item.classList.remove('unread');
        });
        updateNotificationBadge(0);
      });
    });
    
    // Function to update notification badge count
    function updateNotificationBadge(count) {
      notificationBadge.textContent = count;
      notificationBadge.style.display = count > 0 ? 'flex' : 'none';
    }
    
    // Function to load notifications
    function loadNotifications() {
      fetch('../../handlers/admin/get_notifications.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationList.innerHTML = '';
            data.notifications.forEach(notification => {
              const newItem = document.createElement('div');
              newItem.className = `notification-item ${!notification.is_read ? 'unread' : ''}`;
              newItem.innerHTML = `
                <div class="notification-title">${notification.title}</div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">${notification.created_at}</div>
              `;
              
              // Add click handler to redirect to demo if it's a demo reminder
              if (notification.type === 'demo_reminder') {
                newItem.style.cursor = 'pointer';
                newItem.addEventListener('click', function() {
                  window.location.href = 'start_teaching_demo.php?id=' + notification.id;
                });
              }
              
              notificationList.appendChild(newItem);
            });
            // Update badge with count
            updateNotificationBadge(data.count);
          }
        })
        .catch(error => {
          console.error('Error loading notifications:', error);
        });
    }
    
    // Periodically update notifications (every 30 seconds)
    setInterval(loadNotifications, 30000);
    // Initial load
    loadNotifications();
    
    const aiButton = document.getElementById('aiToggleButton');
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
        boxFooter.style.width = 'calc(100% - 60px)';
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
            boxFooter.style.width = 'calc(100% - 60px)';
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
            boxFooter.style.width = 'calc(100% - 60px)';
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

    // AI button click handler
    if (aiButton) {
      aiButton.addEventListener('click', function() {
        // Check if openAIChat function is available, if not wait a bit and try again
        if (typeof openAIChat === 'function') {
          openAIChat();
        } else {
          // Fallback - try to open the chat modal directly
          const aiChatModal = document.getElementById('aiChatModal');
          if (aiChatModal) {
            aiChatModal.classList.add('active');
            const userInput = document.getElementById('userInput');
            if (userInput) userInput.focus();
          }
        }
      });
    }
    
    // Listen for AI thinking events
    document.addEventListener('aiThinkingStarted', function() {
      if (aiButton) {
        aiButton.classList.add('thinking');
      }
    });
    
    document.addEventListener('aiThinkingEnded', function() {
      if (aiButton) {
        aiButton.classList.remove('thinking');
      }
    });

  window.closeAIChat = closeChatModal;
  
  // Fallback close function in case closeChatModal is not available
  if (typeof closeChatModal !== 'function') {
    window.closeAIChat = function() {
      const aiChatModal = document.getElementById('aiChatModal');
      if (aiChatModal) {
        aiChatModal.classList.remove('active');
      }
    };
  }
});
</script>
