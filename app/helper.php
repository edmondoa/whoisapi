<?php
namespace App;

class helper{

    public static function xmlToArray($xmlString)
    {
        // Convert XML to SimpleXMLElement
        try {
            $xmlObject = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
            if ($xmlObject === false) {
                return ['error' => 'Invalid XML'];
            }

            // Convert SimpleXMLElement to array
            $array = json_decode(json_encode($xmlObject), true);
          
            return $array;
            
        } catch (\Exception $e) {
            return ['error' => 'Error processing XML', 'message' => $e->getMessage()];
        }
    }

}
?>