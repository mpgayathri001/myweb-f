import streamlit as st
import requests

# The URL for your Flask backend
BASE_URL = "http://127.0.0.1:5000"  # Change this if your Flask app runs on a different host

def register_user(username, password, email):
    response = requests.post(f"{BASE_URL}/register", json={"username": username, "password": password, "email": email})
    return response.json()

def verify_otp(email, otp):
    response = requests.post(f"{BASE_URL}/verify_otp", json={"email": email, "otp": otp})
    return response.json()

def login_user(username, password):
    response = requests.post(f"{BASE_URL}/login", json={"username": username, "password": password})
    return response.json()

def main():
    st.title("Flask Authentication with Streamlit and OTP")

    option = st.selectbox("Select an option", ("Register", "Login"))

    username = st.text_input("Username")
    password = st.text_input("Password", type="password")
    
    if option == "Register":
        email = st.text_input("Email")
        if st.button("Register"):
            if username and password and email:
                response = register_user(username, password, email)
                st.write(response["message"])
                if "OTP sent" in response["message"]:
                    otp = st.text_input("Enter the OTP sent to your email", type="password")
                    if st.button("Verify OTP"):
                        otp_response = verify_otp(email, otp)
                        st.write(otp_response["message"])
            else:
                st.write("Please enter a username, password, and email.")
    
    elif option == "Login":
        if st.button("Login"):
            if username and password:
                response = login_user(username, password)
                st.write(response["message"])
            else:
                st.write("Please enter a username and password.")

if __name__ == '__main__':
    main()
