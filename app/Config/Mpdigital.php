<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuración de la integración con fiscalia_mpdigital.
 *
 * Puede sobrescribirse desde el .env con:
 *   mpdigital.split = true|false
 */
class Mpdigital extends BaseConfig
{
    /**
     * Cuando es true, las tablas de mpdigital viven en una base de datos
     * separada (grupo de conexión `mpdigital`). Cuando es false, comparten
     * la conexión por defecto, como en la instalación anterior de CI3.
     */
    public bool $split = true;
}
