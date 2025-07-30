<body>
<link rel="stylesheet" href="assets/css/all.css">
<link rel="stylesheet" href="css/styles.css">
<div class="sidebar-container">
    <div class="sidebar">
        <img src="images/exact logo.png" alt="Logo" class="logo">
        <h1 class="admin-title">Admin Panel</h1>
        <ul class="menu"><br><br>
    <li class="dashboard-button"><a href="student_dashboard.php">Something</a></li>
    <li class="evaluation-button"><a href="evaluation_menu.php">Something</a></li>
    <li class="password-button"><a href="manage_password.php">Something</a></li>
    <li class="subject-button"><a href="subject_list.php">Something</a></li>
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
    <span>2024 | Copyright Team Quiet</span>
    <span>OSA Faculty And Faculty Evaluation Management System</span>
</div>

<script>
const section = document.querySelector("section");
const overlay = document.querySelector(".overlay");
const showBtn = document.querySelector(".show-modal");
const closeBtn = document.querySelector(".close-btn");

showBtn.addEventListener("click", () => section.classList.add("active"));
closeBtn.addEventListener("click", () => section.classList.remove("active"));
</script>
</body>
