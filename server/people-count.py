import cv2
from ultralytics import YOLO
import math
import subprocess
import requests
import json
import time
from datetime import datetime
from flask import Flask, Response
from flask_cors import CORS
import threading

app = Flask(__name__)
CORS(app)

# Global variable to store the latest frame
global_frame = None
frame_lock = threading.Lock()

def generate_frames():
    global global_frame
    while True:
        with frame_lock:
            if global_frame is not None:
                # Encode the frame as JPEG
                ret, buffer = cv2.imencode('.jpg', global_frame)
                if not ret:
                    continue
                # Convert to bytes and yield for streaming
                frame_bytes = buffer.tobytes()
                yield (b'--frame\r\n'
                       b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')
        time.sleep(0.033)  # ~30 FPS

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(),
                    mimetype='multipart/x-mixed-replace; boundary=frame')

def send_data_to_php(data):
    url = 'http://172.20.10.2/json-reciver.php'
    headers = {
        'Content-Type': 'application/json'
    }

    try:
        response = requests.post(url, json=data, headers=headers)
        if response.status_code == 200:
            print(f"[{datetime.now()}] Date trimise cu succes!")
        else:
            print(f"[{datetime.now()}] Eroare: {response.status_code}")
    except Exception as e:
        print(f"[{datetime.now()}] Eroare la trimitere: {e}")

def main():
    global global_frame

    # Start Flask server in a separate thread
    threading.Thread(target=lambda: app.run(host='0.0.0.0', port=5000, threaded=True)).start()

    # Initialize YOLO model
    print("Se încarcă modelul YOLO...")
    model = YOLO("yolov8n.pt")

    # Initialize camera
    print("Se inițializează camera...")
    cap = cv2.VideoCapture(0)
    cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1920)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 1080)
    cap.set(cv2.CAP_PROP_FPS, 30)

    last_send_time = 0
    send_interval = 1.0  # Send every second

    while True:
        success, img = cap.read()
        if not success:
            print("Nu s-a putut citi frame-ul!")
            break

        # Run detection
        results = model(img, stream=True)

        detections = []
        for r in results:
            boxes = r.boxes
            for box in boxes:
                x1, y1, x2, y2 = box.xyxy[0]
                x1, y1, x2, y2 = int(x1), int(y1), int(x2), int(y2)
                confidence = float(box.conf[0])
                cls = int(box.cls[0])

                if model.names[cls] == "person":
                    cv2.rectangle(img, (x1, y1), (x2, y2), (255, 0, 255), 3)
                    label = f"Person: {confidence:.2f}"
                    cv2.putText(img, label, (x1, y1-10), cv2.FONT_HERSHEY_SIMPLEX,
                              1, (255, 0, 0), 2)

                    detections.append({
                        "type": "person",
                        "confidence": round(confidence, 2)
                    })

        # Update global frame with detection boxes
        with frame_lock:
            global_frame = img.copy()

        # Send detection data to server
        current_time = time.time()
        if current_time - last_send_time >= send_interval:
            data = {
                "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                "detections": detections,
                "total_detections": len(detections)
            }
            send_data_to_php(data)
            last_send_time = current_time

        # Check for quit command
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()

if __name__ == "__main__":
    main()