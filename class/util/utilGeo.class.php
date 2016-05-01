<?php

  class utilGeo{
    //https://support.groundspeak.com/index.php?pg=kb.page&id=211
    //http://gis.stackexchange.com/questions/120677/best-formula-for-calculating-short-distances-in-utm
    //http://www.uwgb.edu/dutchs/usefuldata/utmformulas.htm
    //http://www.movable-type.co.uk/scripts/latlong-utm-mgrs.html
    //http://www.ibm.com/developerworks/library/j-coordconvert/
    public static function distanceBetweenPoints( $latitudeOrigemASFlt, $longitudeOrigemASFlt, $latitudeDestinationASFlt, $longitudeDestinationASFlt ){
      return ( ( ( acos( sin( ( $latitudeOrigemASFlt * pi() / 180 ) ) * sin( ( $latitudeDestinationASFlt * pi() / 180 ) ) + cos( ( $latitudeOrigemASFlt * pi() / 180 ) ) * cos( ( $latitudeDestinationASFlt * pi() / 180 ) ) * cos( ( ( $longitudeOrigemASFlt - $longitudeDestinationASFlt ) * pi() / 180 ) ) ) ) * 180 / pi() ) * 60 * 1.1515 * 1609.344 );
    }

    public static function coordinatesByDistance( $latitudeASFlt, $longitudeASFlt, $distanceImMetersUInt ){
      //Earth’s radius, sphere
      $radiusLUFlt=6378137;

      //Coordinate offsets in radians
      $dLatitudeLSFlt = $distanceImMetersUInt / $radiusLUFlt;
      $dLongitudeLSFlt = $distanceImMetersUInt / ( $radiusLUFlt * cos( pi() * $latitudeASFlt / 180 ) );

      //OffsetPosition, decimal degrees
      $latitudeUpperLSFlt = $latitudeASFlt + $dLatitudeLSFlt * 180 / pi();
      $longitudeRightLSFlt = $longitudeASFlt + $dLongitudeLSFlt * 180 / pi();

      $distanceImMetersUInt *= -1;

      //Coordinate offsets in radians
      $dLatitudeLSFlt = $distanceImMetersUInt / $radiusLUFlt;
      $dLongitudeLSFlt = $distanceImMetersUInt / ( $radiusLUFlt * cos( pi() * $latitudeASFlt / 180 ) );

      //OffsetPosition, decimal degrees
      $latitudeBottomLSFlt = $latitudeASFlt + $dLatitudeLSFlt * 180 / pi();
      $longitudeLeftLSFlt = $longitudeASFlt + $dLongitudeLSFlt * 180 / pi();

      return array(
        "latitudeUpper"  => $latitudeUpperLSFlt,
        "longitudeRight" => $longitudeRightLSFlt,
        "latitudeBottom" => $latitudeBottomLSFlt,
        "longitudeLeft"  => $longitudeLeftLSFlt
      );
    }
  }