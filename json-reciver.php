<?php
header('Content-Type: application/json');

// Permitem accesul de la orice origine (pentru dezvoltare)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificăm dacă este o cerere POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(array("message" => "Doar metoda POST este permisă"));
  exit();
}

// Citim datele JSON primite
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Verificăm dacă datele sunt valide
if (!$data || !isset($data['timestamp']) || !isset($data['detections']) || !isset($data['total_detections'])) {
  http_response_code(400);
  echo json_encode(array("message" => "Date invalide"));
  exit();
}

// Salvăm imaginea dacă există
if (isset($data['image'])) {
  $img_data = base64_decode($data['image']);
  $timestamp = date('YmdHis');
  $img_path = "./images/detection_$timestamp.jpg";

  // Creăm directorul dacă nu există
  if (!file_exists('./images')) {
    mkdir('./images', 0777, true);
  }

  file_put_contents($img_path, $img_data);
  $data['image_path'] = $img_path;
}

// Procesăm datele
$filename = "./detections.json";
$current_data = array();

// Citim datele existente dacă există
if (file_exists($filename)) {
  $current_data = json_decode(file_get_contents($filename), true);

  // Păstrăm doar ultimele 100 de detecții
  if (count($current_data) > 100) {
    array_shift($current_data);
  }
}

// Adăugăm noile date
$current_data[] = $data;

// Salvăm datele actualizate
if (file_put_contents($filename, json_encode($current_data, JSON_PRETTY_PRINT))) {
  http_response_code(200);
  echo json_encode(array(
    "message" => "Date salvate cu succes",
    "data" => $data
  ));
} else {
  http_response_code(500);
  echo json_encode(array("message" => "Eroare la salvarea datelor"));
}
?>