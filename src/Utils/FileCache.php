<?php


namespace Staro\Graphy\Utils;


final class FileCache {
    /**
     * @var FileCacheConfig
     */
    private $config;

    function __construct(FileCacheConfig $config) {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    function get(string $name, $default = null) {
        $file = $this->file( $name );
        if ( file_exists( $file ) )
            return require $file;
        return $default;
    }

    function set(string $name, $value) {
        $file = $this->file( $name );
        $value = var_export( $value, true );
        if ( !file_exists( dirname( $file ) ) )
            mkdir( dirname( $file ), 0777, true );
        file_put_contents( $file, "<?php return $value;" );
    }

    function file(string $name): string {
        $name = hash( 'md5', $name );
        return $this->config->getDir() . '/' . $name . '.php';
    }
}
