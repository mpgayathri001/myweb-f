import streamlit as st
import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow import keras
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
import matplotlib.pyplot as plt
import seaborn as sns

# Streamlit UI
st.title("DDoS Traffic Detection using Deep Learning")

# File Upload
uploaded_file = st.file_uploader("Upload your dataset (CSV)", type=["csv"])

if uploaded_file is not None:
    df = pd.read_csv(uploaded_file)
    df = df.head(5000)  # Use only the first 5000 rows
    
    st.write("### Dataset Preview")
    st.write(df.head())

    # Check column names
    st.write("Column Names:", df.columns)
    
    # Drop non-numeric columns and 'Packets/Time'
    non_numeric_cols = ['Highest Layer', 'Transport Layer', 'Dest IP', 'Packets/Time']
    df = df.drop(columns=[col for col in non_numeric_cols if col in df.columns], errors='ignore')
    
    # Preprocessing
    st.write("### Data Preprocessing")
    df = df.dropna()  # Remove missing values
    label_encoder = LabelEncoder()
    
    if 'target' in df.columns:
        df['target'] = label_encoder.fit_transform(df['target'])  # Encode target
    else:
        st.error("Target column 'target' not found in dataset.")
        st.stop()
    
    # Ensure only numeric columns are used for training
    X = df.select_dtypes(include=[np.number]).drop(columns=['target'], errors='ignore')
    y = df['target']
    
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)
    
    # Train-test split
    X_train, X_test, y_train, y_test = train_test_split(X_scaled, y, test_size=0.2, random_state=42)
    
    # Build Deep Learning Model
    st.write("### Building Deep Learning Model")
    model = keras.Sequential([
        keras.layers.Dense(64, activation='relu', input_shape=(X_train.shape[1],)),
        keras.layers.Dense(32, activation='relu'),
        keras.layers.Dense(1, activation='sigmoid')
    ])
    model.compile(optimizer='adam', loss='binary_crossentropy', metrics=['accuracy'])
    
    # Train the model
    if st.button("Train Model"):
        history = model.fit(X_train, y_train, epochs=10, batch_size=32, validation_data=(X_test, y_test))
        st.write("### Model Training Completed")
        
        # Predictions
        y_pred = (model.predict(X_test) > 0.5).astype("int32")
        
        # Performance Metrics
        acc = accuracy_score(y_test, y_pred)
        cm = confusion_matrix(y_test, y_pred)
        report = classification_report(y_test, y_pred)
        
        st.write("### Model Performance")
        st.write(f"Accuracy: {acc:.4f}")
        st.text("Classification Report:")
        st.text(report)
        
        # Confusion Matrix Visualization
        fig, ax = plt.subplots()
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', ax=ax)
        st.pyplot(fig)
    
    st.write("### Model Ready for Prediction")
    test_sample = st.text_input("Enter feature values (comma-separated, excluding text-based features)")
    if st.button("Predict"):
        try:
            input_data = np.array([list(map(float, test_sample.split(',')))]).reshape(1, -1)
            input_scaled = scaler.transform(input_data)
            prediction = model.predict(input_scaled)
            result = "DDoS Attack Detected" if prediction > 0.5 else "Normal Traffic"
            st.write(f"Prediction: {result}")
        except ValueError:
            st.error("Invalid input! Ensure all values are numeric and match the expected feature count.")
