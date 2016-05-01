<?php

  class size
  {
    public static function KByte ( $sizeUInt )
    {
      return 1024 * $sizeUInt;
    }

    public static function MByte ( $sizeUInt )
    {
      return 1024 * 1024 * $sizeUInt;
    }

    public static function GByte ( $sizeUInt )
    {
      return 1024 * 1024 * 1024 * $sizeUInt;
    }
  }