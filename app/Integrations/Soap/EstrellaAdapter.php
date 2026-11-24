<?php

namespace App\Integrations\Soap;

use App\Enums\Visibilidad;
use App\Exceptions\ServicioExternoException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class EstrellaAdapter implements EstrellaGateway
{
    public function consultarRuta(string $origen, string $destino = 'Belen'): RutaEstrella
    {
        $url = config('services.estrella.url');

        if (! $url) {
            throw new ServicioExternoException('El servicio de la Estrella no está configurado');
        }

        try {
            // Un Fault SOAP llega normalmente con HTTP 500, por eso no usamos ->throw()
            $respuesta = Http::withBody($this->construirSolicitud($origen, $destino), 'text/xml; charset=utf-8')
                ->withHeaders(['SOAPAction' => config('services.estrella.soap_action')])
                ->connectTimeout(config('services.estrella.timeout'))
                ->timeout(config('services.estrella.timeout'))
                ->post($url);
        } catch (ConnectionException) {
            // Timeout (3 s) o servidor inalcanzable
            throw new ServicioExternoException('El servicio de la Estrella no respondió a tiempo');
        }

        return $this->interpretarRespuesta($respuesta->body());
    }

    private function construirSolicitud(string $origen, string $destino): string
    {
        $origen = htmlspecialchars($origen, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $destino = htmlspecialchars($destino, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                        xmlns:eb="http://belen.beatlech/estrella">
        <soapenv:Body>
            <eb:ConsultarRutaRequest>
            <eb:origen>{$origen}</eb:origen>
            <eb:destino>{$destino}</eb:destino>
            </eb:ConsultarRutaRequest>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;
    }

    private function interpretarRespuesta(string $xml): RutaEstrella
    {
        $previo = libxml_use_internal_errors(true);
        $documento = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previo);

        if (! $documento instanceof SimpleXMLElement) {
            throw new ServicioExternoException('Respuesta ilegible del servicio de la Estrella');
        }

        // Fault SOAP (faultcode soap:Server, faultstring "Cielo cubierto")
        $fault = $documento->xpath('//*[local-name()="Fault"]');
        if (! empty($fault)) {
            $motivo = (string) ($documento->xpath('//*[local-name()="faultstring"]')[0] ?? 'Fault desconocido');

            throw new ServicioExternoException(
                'El servicio de la Estrella devolvió un error',
                [['servicio' => 'EstrellaDeBelenService', 'motivo' => $motivo]]
            );
        }

        $visibilidad = Visibilidad::tryFrom(trim((string) ($documento->xpath('//*[local-name()="visibilidad"]')[0] ?? '')));
        $dias = $documento->xpath('//*[local-name()="diasRestantes"]')[0] ?? null;

        if ($visibilidad === null || $dias === null) {
            throw new ServicioExternoException('Respuesta inesperada del servicio de la Estrella');
        }

        return new RutaEstrella($visibilidad, (int) $dias);
    }
}