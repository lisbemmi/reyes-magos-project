<?php

// Mock local de EstrellaDeBelenService, solo para pruebas en desarrollo.
header('Content-Type: text/xml; charset=utf-8');

$cuerpo = file_get_contents('php://input');
preg_match('/<(?:\w+:)?origen>(.*?)<\/(?:\w+:)?origen>/s', $cuerpo, $m);
$origen = trim($m[1] ?? '');

// Origen "Herodes" -> soap:Fault "Cielo cubierto"
if ($origen === 'Herodes') {
    http_response_code(500);
    echo <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>soap:Server</faultcode>
      <faultstring>Cielo cubierto</faultstring>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
XML;
    exit;
}

// Origen "Lento" -> no responde a tiempo (para probar el timeout de 3 s)
if ($origen === 'Lento') {
    sleep(6);
}

// Origen "Nublada" -> estrella nublada; cualquier otro -> visible
$visibilidad = $origen === 'Nublada' ? 'NUBLADA' : 'VISIBLE';

echo <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:eb="http://belen.beatlech/estrella">
  <soap:Body>
    <eb:ConsultarRutaResponse>
      <eb:visibilidad>{$visibilidad}</eb:visibilidad>
      <eb:diasRestantes>3</eb:diasRestantes>
    </eb:ConsultarRutaResponse>
  </soap:Body>
</soap:Envelope>
XML;