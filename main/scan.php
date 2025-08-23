<!DOCTYPE html>
<html>
<head>
  <title>Scan Document</title>
</head>
<body>
  <h2>Scan and Upload Applicant Document</h2>
  <button onclick="startScan()">Start Scan</button>
  <div id="preview"></div>

  <script>
    function startScan() {
      fetch("http://localhost:5000/scan")
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById("preview").innerHTML = `
              <p>Scanned: ${data.filename}</p>
              <img src="uploads/${data.filename}" width="300"><br>
              <a href="upload.php?file=${data.filename}">Upload to MongoDB</a>
            `;
          } else {
            alert("Scan failed: " + data.message);
          }
        });
    }
  </script>
</body>
</html>
