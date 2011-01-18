<?php

class MyException extends Exception { }

class Test {
    public function testing() {
        try {
            try {
                throw new Exception('foo!');
            } catch (Exception $e) {
                /* rethrow it */
                throw $e;
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
}

$foo = new Test;
$foo->testing();

?>
