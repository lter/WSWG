<?php

interface Transmuter { 




    // write methods


    /**
     * Wrapper for type-specific transmute methods. Each class that 
     * implements this interface needs to implement its own serialization
     * methods for each PHP type supported
     *
     */
    public function __set($p_name, $p_value);


    // read methods


    // other methods


    /**
     * Complete object serialization. Reset for next object in
     * multi-object serialization.
     *
     */

    public function reset();

    /**
     * Write serialization to backend or return serialization. Both
     * implementations are valid.
     */
    public function flush();

}