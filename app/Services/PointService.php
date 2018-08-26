<?php

namespace App\Services;

use App\Point;
use App\CrosswalkPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\PointServiceException;

class PointService {

    /**
     * Obtiene el prefijo de espacio de nombres del
     * xml o lo fija en caso de que no exista.
     *
     * @param \SimpleXMLElement $xml
     * @return string
     */
    protected function xmlNamespace(\SimpleXMLElement $xml) : string {
        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if (strlen($strPrefix) == 0) {
                //Assign an arbitrary namespace prefix.
                $strPrefix = 'a';
            }
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }

        return $strPrefix;
    }

    /**
     * Importa en el sistema los datos obtenidos de un fichero kml/kmz.
     *
     * @param string $fileName
     * @return void
     */
    public function importFromXml(string $fileName) {
        $fileContent = Storage::get("imports/$fileName");

        $xml = simplexml_load_string($fileContent);
        $prefix = $this->xmlNamespace($xml);

        $layers = $xml->xpath("//$prefix:Folder");

        foreach ($layers as $layer) {
            $prefix = $this->xmlNamespace($layer);
            $placemarks = $layer->Placemark;

            foreach ($placemarks as $placemark) {
                $coordinates = (string) $placemark->Point->coordinates;
                $coordinates = explode(',', $coordinates);
    
                if (count($coordinates) == 3) {
                    // Se quitan los atributos del tipo contenidos en el nombre.
                    $type = str_replace('_existent', '', $layer->name);
                    $point = Point::make($type);
                    $point->longitude = floatval($coordinates[0]);
                    $point->latitude = floatval($coordinates[1]);
                    $point->save();
                }
                else {
                    throw new PointServiceException("Punto con número de coordenadas inesperado");
                }
            }
        }
    }

    /**
     * Devuelve el punto con las propiedades de sus correspondientes
     * revisiones. Cada propiedad contiene sus respuestas posibles
     * y el número de usuarios que ha seleccionado cada respuesta.
     *
     * @param \App\Point $point
     * @return \App\Point
     */
    public function crosswalkPointWithDetails(CrosswalkPoint $point) : Point {
        // Se instancian las revisiones del punto.
        $pointVersions = $point->versions()->whereNotNull('properties')->get();

        // Contadores de posibles respuestas para cada propiedad
        $hasCurbRamps = ['true' => 0, 'false' => 0];
        $hasSemaphore = ['true' => 0, 'false' => 0];
        $visibility = ['bad' => 0, 'normal' => 0, 'good' => 0];

        foreach ($pointVersions as $version) {
            // Se le suma 1 al contador de respuestas de cada propiedad.
            // La propia respuesta del usuario se utiliza acceder al
            // array de contadores.
            $hasCurbRamps[$version->properties->hasCurbRamps ? 'true' : 'false'] += 1;
            $hasSemaphore[$version->properties->hasSemaphore ? 'true' : 'false'] += 1;
            $visibility[$version->properties->visibility] += 1;
        }

        // Los datos se asignan al modelo del punto,
        // para que esté encapsulado en el mismo.
        $point["properties"] = [
            'hasCurbRamps' => $hasCurbRamps,
            'hasSemaphore' => $hasSemaphore,
            'visibility' => $visibility,
        ];

        return $point;
    }
}