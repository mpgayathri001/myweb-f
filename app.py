import smtplib
import random
import string
from flask import Flask, request, jsonify
from werkzeug.security import generate_password_hash, check_password_hash
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

app = Flask(__name__)

# Simulating a simple user database with a dictionary
users_db = {}
otp_db = {}  # Stores OTP temporarily for verification
EMAIL_ADDRESS = "gayathri.b2162@gmail.com"  # Your Gmail address
EMAIL_PASSWORD = "dwas ehrn eslh igcx"  # Your Gmail password or app password

# Helper function to send OTP via email
def send_otp_email(user_email, otp):
    subject = "Your OTP Code"
    body = f"Your OTP code is {otp}. It will expire in 10 minutes."

    msg = MIMEMultipart()
    msg['From'] = EMAIL_ADDRESS
    msg['To'] = user_email
    msg['Subject'] = subject
    msg.attach(MIMEText(body, 'plain'))

    try:
        # Connect to Gmail's SMTP server
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(EMAIL_ADDRESS, EMAIL_PASSWORD)
            server.sendmail(EMAIL_ADDRESS, user_email, msg.as_string())
        return True
    except Exception as e:
        print(f"Failed to send email: {e}")
        return False

# Function to generate OTP
def generate_otp():
    return ''.join(random.choices(string.digits, k=6))

@app.route('/register', methods=['POST'])
def register():
    data = request.get_json()

    username = data.get('username')
    password = data.get('password')
    email = data.get('email')

    if username in users_db:
        return jsonify({"message": "User already exists!"}), 400

    # Hash the password before storing it
    hashed_password = generate_password_hash(password)
    users_db[username] = {"password": hashed_password, "email": email}

    # Generate OTP and store it temporarily for verification
    otp = generate_otp()
    otp_db[email] = otp

    # Send OTP email
    if send_otp_email(email, otp):
        return jsonify({"message": "User registered successfully! OTP sent to your email."}), 201
    else:
        return jsonify({"message": "Failed to send OTP. Please try again."}), 500

@app.route('/verify_otp', methods=['POST'])
def verify_otp():
    data = request.get_json()

    email = data.get('email')
    otp_entered = data.get('otp')

    if email not in otp_db:
        return jsonify({"message": "OTP has expired or not generated!"}), 400

    stored_otp = otp_db[email]
    if otp_entered == stored_otp:
        del otp_db[email]  # OTP is used, remove it from the temporary storage
        return jsonify({"message": "OTP verified successfully! You can now log in."}), 200
    else:
        return jsonify({"message": "Invalid OTP!"}), 400

@app.route('/login', methods=['POST'])
def login():
    data = request.get_json()

    username = data.get('username')
    password = data.get('password')

    if username not in users_db:
        return jsonify({"message": "User not found!"}), 404

    # Check if the password matches
    stored_password = users_db[username]["password"]
    if not check_password_hash(stored_password, password):
        return jsonify({"message": "Incorrect password!"}), 400

    return jsonify({"message": "Login successful!"}), 200

if __name__ == '__main__':
    app.run(debug=True)
