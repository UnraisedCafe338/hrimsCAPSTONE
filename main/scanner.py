from flask import Flask, jsonify
from flask_cors import CORS  # NEW
import os
import datetime
import subprocess

app = Flask(__name__)
CORS(app)  # NEW: Enable CORS for all routes

UPLOAD_FOLDER = "../uploads"

SCANNER_TOOL = r"C:\Program Files\NAPS2\NAPS2.Console.exe"
PROFILE_PATH = r"C:\ScannerTools\profiles.xml"
PROFILE_NAME = "Default"

@app.route("/scan")
def scan():
    filename = datetime.datetime.now().strftime("%Y%m%d_%H%M%S") + ".png"
    filepath = os.path.join(UPLOAD_FOLDER, filename)

    try:
        subprocess.run([
    SCANNER_TOOL,
    "--profile", PROFILE_NAME,
    "--output", filepath
], check=True)

        return jsonify(success=True, filename=filename)
    except Exception as e:
        return jsonify(success=False, message=str(e))

if __name__ == "__main__":
    app.run(port=5000)
