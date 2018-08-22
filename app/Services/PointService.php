<?php

namespace App\Services;

use App\Point;
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
}