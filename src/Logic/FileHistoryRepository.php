<?php


namespace Staro\Graphy\Logic;


final class FileHistoryRepository {
    /**
     * @var string
     */
    private $dir;

    function __construct(string $dir) {
        $this->dir = $dir;
    }

    function file(int $month, int $day) {
        $dir = $this->dir;
        return "$dir/$month/$day.php";
    }

    function getHistory(int $month, int $day): array {
        $result = [];
        foreach (range( 1, $day - 1 ) as $day) {
            $file = $this->file( $month, $day );
            if (file_exists( $file )) {
                $result = array_merge( $result, require $file );
            }
        }
        return $result;
    }

    function storeHistory(int $month, int $day, array $history) {
        $file = $this->file( $month, $day );
        $history = var_export( $history, true );
        if (!file_exists( dirname( $file ) ))
            mkdir( dirname( $file ), 0777, true );
        file_put_contents( $file, "<?php return $history;" );
    }
}
