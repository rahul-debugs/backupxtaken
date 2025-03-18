from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from pymongo import MongoClient
from werkzeug.utils import secure_filename
import os

app = Flask(__name__)
CORS(app)  # Enable CORS for frontend requests

# MongoDB configuration
client = MongoClient("mongodb://localhost:27017/")
db = client['orderDB']
orders_collection = db["orders"]

# Ensure the upload folder exists
UPLOAD_FOLDER = "uploads"
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

app.config["UPLOAD_FOLDER"] = UPLOAD_FOLDER

@app.route("/upload", methods=["POST"])
def upload_file():
    if "pdfFile" not in request.files:
        return jsonify({"message": "No file part"}), 400

    file = request.files["pdfFile"]
    if file.filename == "":
        return jsonify({"message": "No selected file"}), 400

    if file and file.filename.endswith(".pdf"):
        filename = secure_filename(file.filename)
        file_path = os.path.join(app.config["UPLOAD_FOLDER"], filename)
        file.save(file_path)

        # Create a publicly accessible URL
        file_url = f"http://localhost:5000/uploads/{filename}"

        # Extract form data (avoid None values)
        order = {
            "name": request.form.get("name", ""),
            "address": request.form.get("address", ""),
            "phone": request.form.get("phone", ""),
            "altPhone": request.form.get("altPhone", ""),
            "paymentMethod": request.form.get("paymentMethod", ""),
            "numPages": int(request.form.get("numPages", "0")),
            "numCopies": int(request.form.get("numCopies", "0")),
            "pdfFilePath": file_url,  # Store accessible URL
        }

        try:
            orders_collection.insert_one(order)
            return jsonify({"message": "File uploaded and order saved successfully"}), 200
        except Exception as e:
            return jsonify({"message": f"Database error: {str(e)}"}), 500
    else:
        return jsonify({"message": "Invalid file type. Only PDF files are allowed"}), 400

# Route to serve uploaded files
@app.route('/uploads/<filename>')
def uploaded_file(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'], filename)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
