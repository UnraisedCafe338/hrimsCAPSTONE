<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Virtual Filing Cabinet</title>
  <style>
.cabinet {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 100px;
  background: white;
  padding: 30px;
  border-radius: 12px;
  height: auto;
  position: relative; /* needed for stacking context */
}

.drawer-back {
  background: rgb(16, 0, 87);
  width: 330px;
  height: 120px;
  border: 3px solid #333;
  border-radius: 6px;
  position: relative;
  cursor: pointer;
  box-shadow: inset 0 0 5px rgba(7, 0, 88, 0.05), 0 4px 10px rgba(0,0,0,0.5);
  overflow: visible; /* allow content to expand out */
  z-index: 1;
  transition: z-index 0.3s ease;
}

.drawer {
  background: rgb(0, 39, 165);
  width: 330px;
  height: 120px;
  border-radius: 6px;
  position: absolute;
  top: 0;
  left: -3px;
  z-index: 2;
  box-shadow: inset 0 0 5px rgba(255,255,255,0.05), 0 4px 10px rgba(0,0,0,0.5);
  transition: top 0.4s ease;
}

.drawer::before {
  content: '';
  position: absolute;
  top: 10px;
  left: 100px;
  width: 130px;
  height: 10px;
  background: white;
  border-radius: 5px;
}

.handle {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 8px;
  background: rgb(255, 230, 0);
  border-radius: 5px;
}

.label {
  position: absolute;
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 13px;
  font-weight: bold;
  text-align: center;
  width: 90%;
}

.folder-content {
  position: absolute;
  top: 30px; /* position below drawer */
  left: 10px;
  width: 90%;
  background-color: #fdd835;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  border-radius: 0 0 6px 6px;
  z-index: 0;
  pointer-events: none; /* so hover doesn't get stuck */
}

.folder-content .tab {
  background: #ffe082;
  height: 15px;
  width: 80%;
  border-radius: 4px;
  margin: 2px auto;
  box-shadow: 0 2px 3px rgba(0,0,0,0.1);
}

/* Show folder content on hover without pushing layout */
.drawer-back:hover {
  z-index: 10;
}

.drawer-back:hover .folder-content {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}
.drawer-back:hover .drawer {
  top: 100px;
}
  </style>
</head>

<body>
<?php include 'sidebar.php'; ?>
<h2 class="header">Document Management</h2><br><br><br>

<div class="content">
  
<div class="cabinet">
  <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

    <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>


  <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

    <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

    <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

      <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

      <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

      <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>

        <div class="drawer-back">
    <div class="folder-content">
      <div class="tab"></div>
      <div class="tab"></div>
      <div class="tab"></div>
    </div>
    <div class="drawer">
      <div class="label">Non-Teaching Staff</div>
      <div class="handle"></div>
    </div>
  </div>
  <!-- more drawer-back items... -->
</div>
  </div>
</body>
</html>
