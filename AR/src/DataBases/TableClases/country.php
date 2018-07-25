<?php
/**
 * Created by PhpStorm.
 * User: user94
 * Date: 25.07.18
 * Time: 13:15
 */

class country extends Arecord
{
    public function tableName()
    {
        $tableName = get_class($this);
        if (($pos = strrpos($tableName, '\\')) !== false)
            return substr($tableName, $pos + 1);
        return $tableName;
    }

}