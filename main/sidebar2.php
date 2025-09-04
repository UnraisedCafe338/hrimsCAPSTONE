<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<link rel="icon" type="..image/png" href="../images/cutie_2.0.png">
<link rel="stylesheet" href="../css/applicant_styles.css?v=1.0">
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

.modal-overlay2 {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content2 {
  background: white;
  padding: 20px 30px;
  border-radius: 8px;
  width: 320px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  font-family: Arial, sans-serif;
}

.modal-content2 h3 {
  margin-bottom: 10px;
}

.modal-buttons2 {
  margin-top: 20px;
  display: flex;
  justify-content: space-around;
}

.modal-buttons2 button {
  padding: 8px 16px;
  font-size: 14px;
  cursor: pointer;
  border: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

#confirm-yes {
  background-color: #28a745;
  color: white;
}

#confirm-yes:hover {
  background-color: #218838;
}

#confirm-no {
  background-color: #dc3545;
  color: white;
}

#confirm-no:hover {
  background-color: #c82333;
}
/* .aibutton.active {
  right: 320px; 
} */
</style>
<div class="sidebar-container">
<div class="sidebar">

    <img src="../images/exact logo.png" alt="Logo" class="logo">
    <h1 class="admin-title">APPLICANTS PANEL</h1>
<ul class="menu">
  <li>
    <a href="#" id="home-link"><i class="fas fa-home"></i>Home</a>
  </li>
  <li class="application-button">
    <a href="#"><i class="fa-solid fa-file-alt"></i>Application</a>
  </li>
</ul>



    

</div>
</div>

<button class="aibutton inactive" onclick="toggleAISidebar()">
  <img src="../images/cutie_2.0.png" alt="AI" />
</button>
<div id="confirm-modal2" class="modal-overlay2" style="display:none;">
  <div class="modal-content2">
    <h3>Confirm Navigation</h3>
    <p>Are you sure you want to cancel your application and go Home?</p>
    <div class="modal-buttons2">
      <button id="confirm-yes">Yes, go Home</button>
      <button id="confirm-no">No, stay here</button>
    </div>
  </div>
</div>
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
  const homeLink = document.getElementById('home-link');
  const modal = document.getElementById('confirm-modal2');
  const confirmYes = document.getElementById('confirm-yes');
  const confirmNo = document.getElementById('confirm-no');

  homeLink.addEventListener('click', function(event) {
    event.preventDefault(); // stop immediate navigation
    modal.style.display = 'flex'; // show modal
  });

  confirmYes.addEventListener('click', function() {
    window.location.href = 'index.php'; // navigate to Home
  });

  confirmNo.addEventListener('click', function() {
    modal.style.display = 'none'; // close modal
  });

  // Optional: close modal on click outside content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
</script>
