from ultralytics import YOLO
import cv2
import time
import torch

# Load models for mask and safety detection
mask_model = YOLO('/home/drumea/Desktop/hackathon/best.pt')  # Path to the mask detection model
safety_model = YOLO('/home/drumea/Desktop/hackathon/yolov8s_custom.pt')  # Path to the second model

# Define class names and custom display labels
mask_classes = ["without_mask", "with_mask", "mask_weared_incorrect"]
safety_classes = ["Helmet", "Safety-Vest"]
label_mapping = {"Helmet": "CAP", "Safety-Vest": "COATS"}  # Custom labels for display

# Open the camera feed (use 0 for the default camera)
cap = cv2.VideoCapture(0)

# Device configuration (use GPU if available)
device = 'cuda' if torch.cuda.is_available() else 'cpu'
mask_model.to(device)
safety_model.to(device)

# Set to process only one frame per second
process_interval = 0.5  # seconds
last_process_time = time.time()
last_detections = []  # Store last detections to overlay on video feed

while True:
    ret, frame = cap.read()
    if not ret:
        print("Failed to capture image")
        break

    current_time = time.time()
    # Process frame if the specified interval has passed
    if current_time - last_process_time >= process_interval:
        last_detections.clear()  # Clear previous detections
        mask_results = mask_model(frame, verbose=False)
        safety_results = safety_model(frame, verbose=False)

        # Process mask detections
        for r in mask_results:
            for c in r.boxes:
                class_name = mask_model.names[int(c.cls)]
                if class_name in mask_classes:
                    x1, y1, x2, y2 = map(int, c.xyxy[0])
                    color = (0, 255, 0) if class_name == "with_mask" else (0, 0, 255)
                    last_detections.append((x1, y1, x2, y2, class_name, color))

        # Process safety detections
        for r in safety_results:
            for c in r.boxes:
                class_name = safety_model.names[int(c.cls)]
                if class_name in safety_classes:
                    display_name = label_mapping.get(class_name, class_name)
                    x1, y1, x2, y2 = map(int, c.xyxy[0])
                    last_detections.append((x1, y1, x2, y2, display_name, (255, 165, 0)))

        last_process_time = current_time  # Update the time of last processing

    # Overlay last detections on each frame
    for (x1, y1, x2, y2, label, color) in last_detections:
        cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
        cv2.putText(frame, label, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

    # Display the frame with detections
    cv2.imshow('FRAME', frame)

    # Exit on ESC key
    if cv2.waitKey(1) & 0xFF == 27:
        break

cap.release()
cv2.destroyAllWindows()
