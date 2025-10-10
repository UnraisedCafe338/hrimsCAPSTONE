<body>
<link rel="stylesheet" href="/hrims/assets/css/all.css">
<link rel="stylesheet" href="/hrims/css/styles.css">
<div class="sidebar-container" id="mainSidebar">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="/hrims/images/exact logo.png" alt="Logo" class="logo">
            <h1 class="admin-title">Admin Panel</h1>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="menu"><br><br>
    <li class="dashboard-button"><a href="student_dashboard.php"><i class="fa-solid fa-gauge"></i><span>Something</span></a></li>
    <li class="evaluation-button"><a href="evaluation_menu.php"><i class="fa-solid fa-clipboard-check"></i><span>Something</span></a></li>
    <li class="password-button"><a href="manage_password.php"><i class="fa-solid fa-key"></i><span>Something</span></a></li>
    <li class="subject-button"><a href="subject_list.php"><i class="fa-solid fa-book"></i><span>Something</span></a></li>
</ul>

    </div>
</div>
<div class="acc-info">
</div>
<section>
    <button class="show-modal"><i class="fa fa-sign-in-alt">&nbsp;&nbsp;&nbsp;Log out</i></button>
    <input type="hidden" name="student_ID" value="<?php echo $_SESSION['student_id']; ?>">

    <div class="modal-box">
        <i class="fas fa-exclamation-triangle"></i>
        <h2>Are you sure you wanna log out?</h2>
        <div class="buttons">
        <form action="logout.php?studentId=<?php echo $_SESSION['student_id']; ?>" method="post">

                <button type="submit" class="sign-out">Yes</button>
                <input type="hidden" name="student_ID" value="<?php echo $_SESSION['student_id']; ?>">
                <button type="button" class="close-btn">No</button>
        </div>
        
            </form>
            
    </div>
    <span class="overlay"></span>
</section>

<div class="box-footer">
    <span>2025 | Copyright Team Quiet</span>
    <span>Human Resources Information Management System</span>
</div>

<script>
const section = document.querySelector("section");
const overlay = document.querySelector(".overlay");
const showBtn = document.querySelector(".show-modal");
const closeBtn = document.querySelector(".close-btn");

showBtn.addEventListener("click", () => section.classList.add("active"));
closeBtn.addEventListener("click", () => section.classList.remove("active"));

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const mainSidebar = document.getElementById('mainSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const boxFooter = document.querySelector('.box-footer');
    
    // Apply saved sidebar state immediately
    const savedSidebarState = localStorage.getItem('sidebarCollapsed');
    const isMobile = window.innerWidth <= 768;
    
    if (!isMobile && savedSidebarState === 'true') {
        mainSidebar.classList.add('collapsed');
        if (boxFooter) {
            boxFooter.style.marginLeft = '60px';
            boxFooter.style.width = 'calc(100% - 60px)';
        }
    }
    
    // Toggle sidebar functionality
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                mainSidebar.classList.toggle('mobile-open');
            } else {
                mainSidebar.classList.toggle('collapsed');
                const isCollapsed = mainSidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                
                if (boxFooter) {
                    if (isCollapsed) {
                        boxFooter.style.marginLeft = '60px';
                        boxFooter.style.width = 'calc(100% - 10px)';
                    } else {
                        boxFooter.style.marginLeft = '248px';
                        boxFooter.style.width = 'calc(100% - 250px)';
                    }
                }
            }
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 768;
        
        if (boxFooter) {
            if (isMobile) {
                boxFooter.style.marginLeft = '0';
                boxFooter.style.width = '100%';
            } else {
                const isCollapsed = mainSidebar.classList.contains('collapsed');
                if (isCollapsed) {
                    boxFooter.style.marginLeft = '60px';
                    boxFooter.style.width = 'calc(100% - 60px)';
                } else {
                    boxFooter.style.marginLeft = '248px';
                    boxFooter.style.width = 'calc(100% - 250px)';
                }
            }
        }
    });
});
</script>
</body>
