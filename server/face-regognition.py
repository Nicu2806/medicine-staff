import requests
import cv2
import face_recognition
import numpy as np
import time
from gtts import gTTS
import os

# Raspberry Pi server URL
raspberry_pi_url = "http://192.168.59.99:5000/capture_image"

# Directory to store registered faces
registered_faces_dir = "/home/drumea/Desktop/hackathon/registered_faces"
os.makedirs(registered_faces_dir, exist_ok=True)

# Known face encodings and names
known_face_encodings = []
known_face_names = []

# Load registered faces from the directory
def load_registered_faces():
    for filename in os.listdir(registered_faces_dir):
        if filename.endswith(".jpg"):
            name = os.path.splitext(filename)[0]
            image_path = os.path.join(registered_faces_dir, filename)
            image = face_recognition.load_image_file(image_path)
            face_encodings = face_recognition.face_encodings(image)

            if face_encodings:
                face_encoding = face_encodings[0]
                known_face_encodings.append(face_encoding)
                known_face_names.append(name)
                print(f"Loaded registered face: {name}")
            else:
                print(f"No face found in {filename}. Skipping this file.")

# Save a new face encoding and image
def save_registered_face(name, frame):
    filepath = os.path.join(registered_faces_dir, f"{name}.jpg")
    cv2.imwrite(filepath, frame)
    face_encoding = face_recognition.face_encodings(frame)[0]
    known_face_encodings.append(face_encoding)
    known_face_names.append(name)
    print(f"{name} has been registered and saved.")

# Request an image from the Raspberry Pi server
def request_image():
    try:
        response = requests.get(raspberry_pi_url, stream=True)
        if response.status_code == 200:
            # Convert the image to an OpenCV format
            img_array = np.frombuffer(response.content, np.uint8)
            frame = cv2.imdecode(img_array, cv2.IMREAD_COLOR)
            return frame
        else:
            print("Failed to get image from Raspberry Pi")
            return None
    except requests.RequestException as e:
        print(f"Error requesting image: {e}")
        return None

# Perform face recognition on the received image
def recognize_face(frame):
    face_locations = face_recognition.face_locations(frame)
    face_encodings = face_recognition.face_encodings(frame, face_locations)

    access_granted = False
    name = "Unknown"

    for face_encoding in face_encodings:
        matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
        if True in matches:
            first_match_index = matches.index(True)
            name = known_face_names[first_match_index]
            access_granted = True
            announce_access(name)
            break

    if not access_granted:
        print("Access denied")

def announce_access(name):
    message = f"Access granted for {name}"
    print(message)
    tts = gTTS(text=message, lang='en')
    tts.save("access_message.mp3")
    os.system("ffplay -nodisp -autoexit access_message.mp3")


# Register a new person by requesting an image from the Raspberry Pi
def register_person():
    name = input("Enter the name of the person to register: ")
    frame = request_image()
    
    if frame is not None:
        face_locations = face_recognition.face_locations(frame)
        face_encodings = face_recognition.face_encodings(frame, face_locations)
        
        if face_encodings:
            save_registered_face(name, frame)
        else:
            print("No face found in the image. Registration failed.")
    else:
        print("Failed to capture image for registration.")

# Continuous recognition loop
def continuous_recognition():
    print("Starting continuous recognition. Press Ctrl+C to stop.")
    try:
        while True:
            frame = request_image()
            if frame is not None:
                recognize_face(frame)
            time.sleep(3)
    except KeyboardInterrupt:
        print("Stopping continuous recognition.")

# Main menu for choosing actions
def main_menu():
    load_registered_faces()  # Load faces at startup

    while True:
        print("\n--- Main Menu ---")
        print("1. Register a New Person")
        print("2. Start Continuous Recognition")
        print("3. Exit")

        choice = input("Select an option: ")

        if choice == '1':
            register_person()
        elif choice == '2':
            continuous_recognition()
        elif choice == '3':
            print("Exiting the program.")
            break
        else:
            print("Invalid choice. Please select 1, 2, or 3.")

# Run the menu
if __name__ == "__main__":
    main_menu()